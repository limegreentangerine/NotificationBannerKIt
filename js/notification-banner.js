class NotificationBanner extends HTMLElement {
	connectedCallback() {
		this.notification = this.querySelector('.nb-notification');

		if (!this.notification) return;

		if (document.body.classList.contains('maintenance_mode')) {
			this.remove();
			return;
		}

		this.bannerStyle = this.notification.dataset.style ?? 'default';
		this.bannerId = this.notification.dataset.bannerId ?? 'default';
		this.bannerVersion = this.notification.dataset.bannerVersion ?? '';
		this.cookieName = `nb_closed_${this.bannerId}`;

		this.positionNotification();
	}

	disconnectedCallback() {
		this.dismissButton?.removeEventListener('click', this._onDismiss);
		if (this._onKeydown) {
			document.removeEventListener('keydown', this._onKeydown);
		}
	}

	positionNotification() {
		const ccmPage = document.querySelector('.ccm-page');

		if (!ccmPage) {
			console.warn('NotificationBanner: .ccm-page not found, cannot position notification.');
			return;
		}

		ccmPage.prepend(this.notification);
		this.init();
	}

	init() {
		document.body.classList.add(`nb-${this.bannerStyle}`);

		if (this.readCookie(this.cookieName) !== this.bannerVersion) {
			this.notification.classList.add('nb-show');

			if (this.bannerStyle === 'popup') {
				this.previouslyFocusedElement = document.activeElement;
				this.wrapper = this.notification.querySelector('.notification-popup__wrapper');
				this.wrapper?.setAttribute('tabindex', '-1');
				this.wrapper?.focus();
				this._onKeydown = this.handleKeydown.bind(this);
				document.addEventListener('keydown', this._onKeydown);
			}
		}

		this.dismissButton = this.notification.querySelector('.close-nb-notification');

		if (this.dismissButton) {
			this._onDismiss = this.closeNotification.bind(this);
			this.dismissButton.addEventListener('click', this._onDismiss);
		}
	}

	handleKeydown(event) {
		if (event.key === 'Escape') {
			this.closeNotification(event);
		}
	}

	closeNotification(event) {
		event.preventDefault();
		this.notification.classList.remove('nb-show');
		this.createCookie(this.cookieName, this.bannerVersion, 30);

		if (this._onKeydown) {
			document.removeEventListener('keydown', this._onKeydown);
			this._onKeydown = null;
		}

		this.previouslyFocusedElement?.focus();
	}

	/**
	 * @param {string} name
	 * @param {string|number} value
	 * @param {number} days
	 */
	createCookie(name, value, days) {
		let expires = '';

		if (days) {
			const date = new Date();
			date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
			expires = `; expires=${date.toUTCString()}`;
		}

		const secure = location.protocol === 'https:' ? '; Secure' : '';

		document.cookie = `${name}=${value}${expires}; path=/; SameSite=Lax${secure}`;
	}

	/**
	 * @param {string} name
	 * @returns {string|null}
	 */
	readCookie(name) {
		const nameEQ = `${name}=`;
		for (let c of document.cookie.split(';')) {
			c = c.trimStart();
			if (c.startsWith(nameEQ)) return c.substring(nameEQ.length);
		}
		return null;
	}
}

if (!customElements.get('component-notification')) {
	customElements.define('component-notification', NotificationBanner);
}
