/**
 * Scroll Animations Manager
 * Handles scroll-triggered animations, page transitions, and cinematic effects
 * Uses Intersection Observer API for performance
 */

class ScrollAnimationsManager {
	constructor(options = {}) {
		this.options = {
			rootMargin: options.rootMargin || '0px 0px -100px 0px',
			threshold: options.threshold || 0.1,
			animationDuration: options.animationDuration || 0.7,
		};

		this.observer = null;
		this.elements = [];
		this.pageTransitionEnabled = options.enablePageTransitions !== false;
		this.init();
	}

	init() {
		// Initialize Intersection Observer for scroll animations
		this.setupIntersectionObserver();

		// Setup page transitions
		if (this.pageTransitionEnabled) {
			this.setupPageTransitions();
		}

		// Trigger auto-animations on page load
		this.setupAutoAnimations();

		// Initial check for elements already in view
		this.observeElements();
	}

	setupAutoAnimations() {
		// Find all elements with auto-animate attribute (for page load animations)
		const autoAnimateElements = document.querySelectorAll('[data-auto-animate]');

		autoAnimateElements.forEach((element) => {
			// Store original text content
			const originalText = element.textContent;
			const charCount = originalText.length;
			
			// Calculate character count for typewriter effect
			if (element.dataset.autoAnimate === 'typewriter' || element.dataset.scrollAnimate === 'typewriter') {
				// Store original text as data attribute
				element.dataset.originalText = originalText;
				element.style.setProperty('--char-count', charCount);
				
				// Keep text visible initially for layout
			}

			// Use a small delay to ensure DOM is ready
			setTimeout(() => {
				this.animateElement(element);
			}, 50);
		});
	}

	setupIntersectionObserver() {
		this.observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					this.animateElement(entry.target);
					// Only animate once unless data-scroll-repeat is set
					if (!entry.target.dataset.scrollRepeat) {
						this.observer.unobserve(entry.target);
					}
				}
			});
		}, this.options);
	}

	observeElements() {
		// Find all elements with scroll animation attributes
		const animatedElements = document.querySelectorAll('[data-scroll-animate]');

		animatedElements.forEach((element) => {
			this.elements.push(element);
			this.observer.observe(element);

			// Add stagger delay if element has nth-child position
			if (element.dataset.scrollStagger !== false) {
				const parent = element.parentElement;
				if (parent) {
					const index = Array.from(parent.children).indexOf(element);
					if (index > 0) {
						const staggerClass = `scroll-stagger-${Math.min(index + 1, 6)}`;
						element.classList.add(staggerClass);
					}
				}
			}
		});
	}

	animateElement(element) {
		// Set animation type - prioritize auto-animate over scroll-animate
		const animationType = element.dataset.autoAnimate || element.dataset.scrollAnimate || 'fade-up';
		const duration = element.dataset.scrollDuration || `${this.options.animationDuration}s`;
		const delay = element.dataset.scrollDelay || '0s';

		// Apply animation
		element.style.setProperty('--scroll-anim', animationType);
		element.style.setProperty('--scroll-duration', duration);
		element.style.setProperty('--scroll-delay', delay);

		// Trigger animation
		element.classList.add('scroll-in');

		// Emit custom event
		element.dispatchEvent(new CustomEvent('scroll-animated', { bubbles: true }));
	}

	parseDuration(durationStr) {
		if (typeof durationStr === 'number') return durationStr;
		const match = String(durationStr).match(/^([\d.]+)(ms|s)?$/);
		if (!match) return 1000;
		const value = parseFloat(match[1]);
		const unit = match[2] || 's';
		return unit === 'ms' ? value : value * 1000;
	}

	// Initialize timeline animations (ScrollAnimationsManager scope)
	initTimelineAnimations() {
		const timelineItems = document.querySelectorAll('.timeline-item');
		
		if (timelineItems.length === 0) return;

		// Create intersection observer for timeline items
		const timelineObserver = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('animate-in');
					// Only animate once
					timelineObserver.unobserve(entry.target);
				}
			});
		}, {
			rootMargin: '0px 0px -50px 0px',
			threshold: 0.1
		});

		timelineItems.forEach(item => {
			timelineObserver.observe(item);
		});
	}

	setupPageTransitions() {
		// Add page transition class to main content on load
		document.addEventListener('DOMContentLoaded', () => {
			const mainContent = document.querySelector('.site-main') || document.body;
			// Skip adding page-transition on pages that include a .home-page wrapper
			if (!document.querySelector('.home-page')) {
				mainContent.classList.add('page-transition');
			}
		});

		// Handle link clicks for smooth transitions
		document.addEventListener('click', (e) => {
			const link = e.target.closest('a');

			// Skip external links, anchor links, and special cases
			if (!link || 
				link.target === '_blank' || 
				link.hostname !== window.location.hostname ||
				link.hash ||
				link.dataset.noTransition ||
				e.ctrlKey || 
				e.metaKey) {
				return;
			}

			// Prevent default navigation
			e.preventDefault();

			// Trigger exit animation
			this.transitionToPage(link.href);
		});
	}

	transitionToPage(url) {
		const mainContent = document.querySelector('.site-main') || document.body;

		// Add exit animation
		mainContent.classList.add('page-exit');

		// Navigate after animation completes
		setTimeout(() => {
			window.location.href = url;
		}, 500);
	}

	// Parallax effect for scroll
	setupParallax() {
		const parallaxElements = document.querySelectorAll('[data-parallax]');

		if (parallaxElements.length === 0) return;

		window.addEventListener('scroll', () => {
			parallaxElements.forEach((element) => {
				const speed = element.dataset.parallax || 0.5;
				const yPos = window.scrollY * speed;
				element.style.transform = `translateY(${yPos}px)`;
			});
		}, { passive: true });
	}

	// Force re-observe elements (useful after AJAX loads)
	refreshObserver() {
		this.observeElements();
	}

	// Add event listener for dynamic content
	observeNewElements() {
		// Use MutationObserver to watch for new elements
		const mutationObserver = new MutationObserver((mutations) => {
			mutations.forEach((mutation) => {
				mutation.addedNodes.forEach((node) => {
					if (node.nodeType === 1) { // Element node
						if (node.dataset && node.dataset.scrollAnimate) {
							this.observer.observe(node);
						}
						// Also check children
						node.querySelectorAll?.('[data-scroll-animate]').forEach((el) => {
							this.observer.observe(el);
						});
					}
				});
			});
		});

		mutationObserver.observe(document.body, {
			childList: true,
			subtree: true,
		});
	}
}

/**
 * Loading Skeleton Manager
 * Handles skeleton loaders and smooth transitions to real content
 */
class LoadingSkeletonManager {
	constructor() {
		this.skeletonMap = new Map();
	}

	createSkeleton(identifier, count = 1, type = 'card') {
		const container = document.getElementById(identifier);
		if (!container) return;

		const skeletons = this.createSkeletonElements(count, type);
		this.skeletonMap.set(identifier, container.innerHTML);

		container.innerHTML = '';
		skeletons.forEach((skeleton) => {
			container.appendChild(skeleton);
		});
	}

	createSkeletonElements(count, type) {
		const elements = [];

		for (let i = 0; i < count; i++) {
			let skeleton;

			switch (type) {
				case 'card':
					skeleton = this.createCardSkeleton();
					break;
				case 'text':
					skeleton = this.createTextSkeleton();
					break;
				case 'avatar':
					skeleton = this.createAvatarSkeleton();
					break;
				default:
					skeleton = this.createCardSkeleton();
			}

			elements.push(skeleton);
		}

		return elements;
	}

	createCardSkeleton() {
		const div = document.createElement('div');
		div.className = 'skeleton skeleton-card';
		div.innerHTML = `
			<div class="skeleton skeleton-card-image"></div>
			<div class="skeleton-card-content">
				<div class="skeleton skeleton-card-title"></div>
				<div class="skeleton skeleton-card-description"></div>
				<div class="skeleton skeleton-card-description" style="width: 80%;"></div>
			</div>
		`;
		return div;
	}

	createTextSkeleton() {
		const div = document.createElement('div');
		div.className = 'skeleton skeleton-text';
		return div;
	}

	createAvatarSkeleton() {
		const div = document.createElement('div');
		div.className = 'skeleton skeleton-avatar';
		return div;
	}

	restoreContent(identifier) {
		const container = document.getElementById(identifier);
		if (!container || !this.skeletonMap.has(identifier)) return;

		const originalContent = this.skeletonMap.get(identifier);

		// Fade out skeleton
		container.style.opacity = '0.7';
		container.style.filter = 'blur(2px)';

		setTimeout(() => {
			container.innerHTML = originalContent;
			container.style.opacity = '1';
			container.style.filter = 'blur(0)';

			// Trigger re-animation for new content
			if (window.scrollAnimationsManager) {
				window.scrollAnimationsManager.refreshObserver();
			}
		}, 200);
	}

	// Initialize timeline animations
	// Initialize timeline animations (was mistakenly in LoadingSkeletonManager)
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => {
		window.scrollAnimationsManager = new ScrollAnimationsManager({
			rootMargin: '0px 0px -80px 0px',
			threshold: 0.1,
		});

		window.loadingSkeletonManager = new LoadingSkeletonManager();

		// Setup parallax if elements exist
		window.scrollAnimationsManager.setupParallax();

		// Watch for dynamic content
		window.scrollAnimationsManager.observeNewElements();
			// Initialize timeline animations
			window.scrollAnimationsManager.initTimelineAnimations();
	});
} else {
	window.scrollAnimationsManager = new ScrollAnimationsManager({
		rootMargin: '0px 0px -80px 0px',
		threshold: 0.1,
	});

	window.loadingSkeletonManager = new LoadingSkeletonManager();
	window.scrollAnimationsManager.setupParallax();
	window.scrollAnimationsManager.observeNewElements();
	// Initialize timeline animations
	window.scrollAnimationsManager.initTimelineAnimations();
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
	module.exports = { ScrollAnimationsManager, LoadingSkeletonManager };
}
