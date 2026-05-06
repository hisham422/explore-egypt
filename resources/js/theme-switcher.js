/**
 * Theme Switcher
 * Handles dark/light mode toggle with localStorage persistence
 * Respects system preferences on first visit
 */

class ThemeSwitcher {
	constructor(options = {}) {
		this.storageKey = options.storageKey || 'theme-preference';
		this.darkClass = options.darkClass || 'dark';
		this.lightClass = options.lightClass || 'light';
		this.systemPreference = this.getSystemPreference();
		this.currentTheme = this.loadTheme();
		this.callbacks = [];

		this.init();
	}

	/**
	 * Initialize theme switcher
	 */
	init() {
		// Apply saved theme on page load
		this.applyTheme(this.currentTheme);

		// Listen for system theme changes
		if (window.matchMedia) {
			window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
				if (!this.hasSavedPreference()) {
					this.setTheme(e.matches ? 'dark' : 'light');
				}
			});
		}

		// Expose toggle to window for use in other scripts
		window.themeSwitcher = this;
	}

	/**
	 * Get system color scheme preference
	 * @returns {string} 'dark' or 'light'
	 */
	getSystemPreference() {
		if (!window.matchMedia) return 'light';
		return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
	}

	/**
	 * Check if user has saved a preference
	 * @returns {boolean}
	 */
	hasSavedPreference() {
		return localStorage.getItem(this.storageKey) !== null;
	}

	/**
	 * Load theme from localStorage or system preference
	 * @returns {string} 'dark' or 'light'
	 */
	loadTheme() {
		const saved = localStorage.getItem(this.storageKey);
		if (saved) {
			return saved;
		}
		return this.systemPreference;
	}

	/**
	 * Set and persist theme
	 * @param {string} theme - 'dark' or 'light'
	 */
	setTheme(theme) {
		if (theme !== 'dark' && theme !== 'light') {
			console.warn('Invalid theme:', theme);
			return;
		}

		this.currentTheme = theme;
		localStorage.setItem(this.storageKey, theme);
		this.applyTheme(theme);
		this.notifyCallbacks(theme);
	}

	/**
	 * Apply theme to document
	 * @param {string} theme - 'dark' or 'light'
	 */
	applyTheme(theme) {
		const root = document.documentElement;
		const body = document.body;
		const activeClass = theme === 'dark' ? this.darkClass : this.lightClass;

		root.className = root.className
			.split(/\s+/)
			.filter((className) => className && className !== this.darkClass && className !== this.lightClass)
			.concat(activeClass)
			.join(' ');

		if (body) {
			body.className = body.className
				.split(/\s+/)
				.filter((className) => className && className !== this.darkClass && className !== this.lightClass)
				.concat(activeClass)
				.join(' ');
		}

		// Update meta theme-color for mobile browsers
		const metaThemeColor = document.querySelector('meta[name="theme-color"]');
		if (metaThemeColor) {
			metaThemeColor.setAttribute(
				'content',
				theme === 'dark' ? '#1b2430' : '#ffffff'
			);
		}

		// Store for accessibility
		document.documentElement.setAttribute('data-theme', theme);
	}

	/**
	 * Toggle between dark and light theme
	 */
	toggle() {
		const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
		this.setTheme(newTheme);
	}

	/**
	 * Get current theme
	 * @returns {string}
	 */
	getTheme() {
		return this.currentTheme;
	}

	/**
	 * Check if dark mode is active
	 * @returns {boolean}
	 */
	isDark() {
		return this.currentTheme === 'dark';
	}

	/**
	 * Register callback for theme changes
	 * @param {function} callback
	 */
	onChange(callback) {
		if (typeof callback === 'function') {
			this.callbacks.push(callback);
		}
	}

	/**
	 * Notify all registered callbacks
	 * @param {string} theme
	 */
	notifyCallbacks(theme) {
		this.callbacks.forEach((callback) => {
			try {
				callback(theme);
			} catch (e) {
				console.error('Theme callback error:', e);
			}
		});
	}

	/**
	 * Reset to system preference
	 */
	resetToSystem() {
		localStorage.removeItem(this.storageKey);
		this.currentTheme = this.systemPreference;
		this.applyTheme(this.currentTheme);
		this.notifyCallbacks(this.currentTheme);
	}
}

/**
 * Alpine.js Component for theme toggle button
 */
window.themeToggle = () => ({
	isDark: false,
	init() {
		if (window.themeSwitcher) {
			this.isDark = window.themeSwitcher.isDark();

			// Listen for theme changes
			window.themeSwitcher.onChange((theme) => {
				this.isDark = theme === 'dark';
			});
		}
	},
	toggle() {
		if (window.themeSwitcher) {
			window.themeSwitcher.toggle();
			this.isDark = window.themeSwitcher.isDark();
		}
	},
	getIcon() {
		return this.isDark ? '☀️' : '🌙';
	},
	getLabel() {
		return this.isDark ? 'Switch to light mode' : 'Switch to dark mode';
	},
});

// Initialize theme switcher automatically
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => {
		window.globalThemeSwitcher = new ThemeSwitcher({
			storageKey: 'theme-preference',
			darkClass: 'dark',
		});
	});
} else {
	window.globalThemeSwitcher = new ThemeSwitcher({
		storageKey: 'theme-preference',
		darkClass: 'dark',
	});
}
