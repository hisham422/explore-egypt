import './bootstrap';

import Alpine from 'alpinejs';
import './scroll-animations';
import './theme-switcher';

window.Alpine = Alpine;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const feedbackElement = document.getElementById('favorite-feedback');

window.tourismNavigation = () => ({
	mobileOpen: false,
	userMenuOpen: false,
	init() {
		this.boundDocumentClick = (event) => {
			if (this.mobileOpen) {
				const clickedInsideMobilePanel = this.$refs.mobilePanel?.contains(event.target);
				const clickedMobileToggle = this.$refs.mobileToggle?.contains(event.target);

				if (!clickedInsideMobilePanel && !clickedMobileToggle) {
					this.mobileOpen = false;
				}
			}

			if (this.userMenuOpen && !this.$refs.userMenu?.contains(event.target)) {
				this.userMenuOpen = false;
			}
		};

		document.addEventListener('click', this.boundDocumentClick);
	},
	toggleMobileMenu() {
		this.mobileOpen = !this.mobileOpen;

		if (this.mobileOpen) {
			this.userMenuOpen = false;
		}
	},
	closeMobileMenu() {
		this.mobileOpen = false;
	},
	toggleUserMenu() {
		this.userMenuOpen = !this.userMenuOpen;

		if (this.userMenuOpen) {
			this.mobileOpen = false;
		}
	},
	closeUserMenu() {
		this.userMenuOpen = false;
	},
	closeMenus() {
		this.mobileOpen = false;
		this.userMenuOpen = false;
	},
	handleResize() {
		if (window.innerWidth >= 768) {
			this.mobileOpen = false;
		}
	},
});

window.adminImageUpload = ({ name, currentUrl, placeholderUrl, accept, deleteUrl }) => ({
	name,
	accept,
	currentUrl,
	placeholderUrl,
	deleteUrl,
	isDragging: false,
	errorMessage: '',
	fileName: currentUrl ? currentUrl.split('/').pop()?.split('?')[0] || '' : '',
	previewUrl: currentUrl || null,

	hasSelection: Boolean(currentUrl),
	objectUrl: null,
	isDeleting: false,
	init() {
	},
	openPicker() {
		this.$refs.input?.click();
	},
	setError(message) {
		this.errorMessage = message;
		window.clearTimeout(this.errorTimeout);
		this.errorTimeout = window.setTimeout(() => {
			this.errorMessage = '';
		}, 2600);
	},
	validateFile(file) {
		if (!file) {
			return 'Please select a file.';
		}

		return '';
	},
	setFile(file) {
		const validationError = this.validateFile(file);

		if (validationError) {
			this.setError(validationError);
			this.$refs.input.value = '';
			return;
		}

		this.errorMessage = '';
		this.fileName = file.name;
		if (this.objectUrl) {
			URL.revokeObjectURL(this.objectUrl);
		}
		this.objectUrl = URL.createObjectURL(file);
		this.previewUrl = file.type.startsWith('image/') ? this.objectUrl : null;
		this.hasSelection = true;
	},
	handleChange(event) {
		const [file] = event.target.files || [];

		if (file) {
			this.setFile(file);
		}
	},
	handleDragOver() {
		this.isDragging = true;
	},
	handleDragLeave(event) {
		if (event.relatedTarget && this.$el.contains(event.relatedTarget)) {
			return;
		}

		this.isDragging = false;
	},
	handleDrop(event) {
		this.isDragging = false;

		const [file] = event.dataTransfer?.files || [];

		if (!file) {
			return;
		}

		this.$refs.input.files = event.dataTransfer.files;
		this.setFile(file);
	},
	async deleteCurrentImage() {
		if (!this.currentUrl || !this.deleteUrl) {
			return;
		}

		if (!confirm('Are you sure you want to delete this image?')) {
			return;
		}

		this.isDeleting = true;
		try {
			const response = await fetch(this.deleteUrl, {
				method: 'DELETE',
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
				},
			});

			if (!response.ok) {
				throw new Error('Failed to delete image');
			}

			this.currentUrl = null;
			this.previewUrl = null;
			this.hasSelection = this.objectUrl ? true : false;
			this.setError('Image deleted successfully');
		} catch (error) {
			this.setError('Error deleting image');
			console.error('Delete error:', error);
		} finally {
			this.isDeleting = false;
		}
	},
	async resetSelection() {
		// Clear new file selection
		if (this.objectUrl) {
			URL.revokeObjectURL(this.objectUrl);
			this.objectUrl = null;
		}
		this.$refs.input.value = '';
		this.errorMessage = '';

		// If there's a current image and way to delete it, offer to delete
		if (this.currentUrl && this.deleteUrl) {
			await this.deleteCurrentImage();
		}

		// Reset the visible state
		this.hasSelection = Boolean(this.currentUrl);
		this.fileName = this.currentUrl ? this.currentUrl.split('/').pop()?.split('?')[0] || '' : '';
		this.previewUrl = this.currentUrl || null;
	},
});

window.adminMultiImageUpload = ({ accept, fieldName, maxFileSize = 10485760 }) => ({
	accept,
	fieldName,
	maxFileSize,
	isDragging: false,
	dragIndex: null,
	errorMessage: '',
	submitError: '',
	isUploading: false,
	uploadProgress: 0,
	files: [],
	previews: [],
	errorTimeout: null,
	form: null,
	boundSubmitHandler: null,
	get summaryText() {
		if (!this.files.length) {
			return 'No files selected yet';
		}

		return `${this.files.length} file${this.files.length === 1 ? '' : 's'} selected`;
	},
	init() {
		this.form = this.$el.closest('form');

		if (this.form) {
			this.boundSubmitHandler = (event) => this.handleSubmit(event);
			this.form.addEventListener('submit', this.boundSubmitHandler);
		}

		this.rebuildPreviews();
	},
	openPicker() {
		this.$refs.input?.click();
	},
	setError(message) {
		this.errorMessage = message;
		window.clearTimeout(this.errorTimeout);
		this.errorTimeout = window.setTimeout(() => {
			this.errorMessage = '';
		}, 2600);
	},
	setSubmitError(message) {
		this.submitError = message;
		window.clearTimeout(this.errorTimeout);
		this.errorTimeout = window.setTimeout(() => {
			this.submitError = '';
		}, 4000);
	},
	resetSubmissionState() {
		this.isUploading = false;
		this.uploadProgress = 0;
	},
	syncInputFiles() {
		const dataTransfer = new DataTransfer();
		this.files.forEach((file) => dataTransfer.items.add(file));
		this.$refs.input.files = dataTransfer.files;
	},
	detectFileType(file) {
		const name = file.name.toLowerCase();
		const extension = name.includes('.') ? name.split('.').pop() : '';

		if (file.type.startsWith('video/') || extension === 'mp4') {
			return 'video';
		}

		if (file.type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'webp'].includes(extension)) {
			return 'image';
		}

		return '';
	},
	validateFile(file) {
		if (!file) {
			return 'Please select a file.';
		}

		if (file.size > this.maxFileSize) {
			return `${file.name} is larger than the 10 MB limit.`;
		}

		if (!this.detectFileType(file)) {
			return `${file.name} is not a supported image or video.`;
		}

		return '';
	},
	rebuildPreviews() {
		this.previews.forEach((preview) => URL.revokeObjectURL(preview.url));
		this.previews = this.files.map((file, index) => ({
			id: `${file.name}-${file.lastModified}-${index}`,
			name: file.name,
			url: URL.createObjectURL(file),
			type: file.type || '',
			progress: 0,
			state: 'ready',
		}));
	},
	addFiles(nextFiles) {
		if (!nextFiles.length) {
			this.setError('Please select at least one file.');
			return;
		}

		const errors = [];
		const acceptedFiles = [];

		nextFiles.forEach((file) => {
			const validationError = this.validateFile(file);

			if (validationError) {
				errors.push(validationError);
				return;
			}

			const duplicate = this.files.some((existingFile) => (
				existingFile.name === file.name
				&& existingFile.size === file.size
				&& existingFile.lastModified === file.lastModified
			));

			if (!duplicate) {
				acceptedFiles.push(file);
			}
		});

		if (errors.length) {
			this.setError(errors[0]);
		}

		if (!acceptedFiles.length) {
			return;
		}

		this.errorMessage = '';
		this.files = [...this.files, ...acceptedFiles];
		this.rebuildPreviews();
		this.syncInputFiles();
	},
	handleChange(event) {
		const selectedFiles = Array.from(event.target.files || []);
		this.files = [];
		this.addFiles(selectedFiles);
	},
	handleDragOver() {
		this.isDragging = true;
	},
	handleDragLeave(event) {
		if (event.relatedTarget && this.$el.contains(event.relatedTarget)) {
			return;
		}

		this.isDragging = false;
	},
	handleDrop(event) {
		this.isDragging = false;
		const droppedFiles = Array.from(event.dataTransfer?.files || []);

		if (!droppedFiles.length) {
			return;
		}

		this.addFiles(droppedFiles);
	},
	removeAt(index) {
		if (index < 0 || index >= this.files.length) {
			return;
		}

		this.files.splice(index, 1);
		this.rebuildPreviews();
		this.syncInputFiles();
	},
	startReorder(index) {
		this.dragIndex = index;
	},
	handleReorderOver(index) {
		if (this.dragIndex === null || this.dragIndex === index) {
			return;
		}
	},
	dropReorder(targetIndex) {
		if (
			this.dragIndex === null
			|| targetIndex < 0
			|| targetIndex >= this.files.length
			|| this.dragIndex === targetIndex
		) {
			this.finishReorder();
			return;
		}

		const [movedFile] = this.files.splice(this.dragIndex, 1);
		this.files.splice(targetIndex, 0, movedFile);

		this.rebuildPreviews();
		this.syncInputFiles();
		this.finishReorder();
	},
	finishReorder() {
		this.dragIndex = null;
	},
	updateProgress(uploadedBytes, totalBytes) {
		if (!this.previews.length || !totalBytes) {
			this.uploadProgress = 0;
			return;
		}

		const clampedLoaded = Math.max(0, Math.min(uploadedBytes, totalBytes));
		const percentage = Math.min(100, Math.round((clampedLoaded / totalBytes) * 100));
		this.uploadProgress = percentage;

		let startRatio = 0;
		const totalFileBytes = this.files.reduce((sum, file) => sum + file.size, 0) || totalBytes;

		this.previews.forEach((preview, index) => {
			const file = this.files[index];
			const fileRatio = file ? (file.size / totalFileBytes) : 0;
			const endRatio = startRatio + fileRatio;
			const fileProgress = fileRatio > 0
				? Math.max(0, Math.min(1, (clampedLoaded / totalBytes - startRatio) / fileRatio))
				: (clampedLoaded >= totalBytes ? 1 : 0);

			preview.progress = Math.round(fileProgress * 100);
			preview.state = percentage >= 100 ? 'done' : (preview.progress > 0 ? 'uploading' : 'ready');
			startRatio = endRatio;
		});
	},
	handleSubmit(event) {
		if (!this.form || this.isUploading) {
			return;
		}

		event.preventDefault();
		this.submitError = '';
		this.isUploading = true;
		this.uploadProgress = 0;
		this.previews.forEach((preview) => {
			preview.progress = 0;
			preview.state = 'uploading';
		});

		const formData = new FormData(this.form);
		const xhr = new XMLHttpRequest();
		xhr.open((this.form.method || 'POST').toUpperCase(), this.form.action, true);
		xhr.responseType = 'json';
		xhr.setRequestHeader('Accept', 'application/json');
		xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

		xhr.upload.addEventListener('progress', (progressEvent) => {
			if (!progressEvent.lengthComputable) {
				return;
			}

			this.updateProgress(progressEvent.loaded, progressEvent.total);
		});

		xhr.addEventListener('error', () => {
			this.resetSubmissionState();
			this.setSubmitError('The upload could not be completed. Please try again.');
		});

		xhr.addEventListener('load', () => {
			const response = xhr.response;

			if (xhr.status >= 200 && xhr.status < 300) {
				this.updateProgress(1, 1);
				this.resetSubmissionState();

				if (response && response.redirect) {
					window.location.href = response.redirect;
					return;
				}

				if (xhr.responseURL) {
					window.location.href = xhr.responseURL;
					return;
				}

				window.location.reload();
				return;
			}

			this.resetSubmissionState();

			if (xhr.status === 422 && response?.errors) {
				const firstKey = Object.keys(response.errors)[0];
				const firstMessage = firstKey ? response.errors[firstKey][0] : 'Please check the highlighted fields.';
				this.setSubmitError(firstMessage);
				return;
			}

			this.setSubmitError(response?.message || 'Unable to save the attraction.');
		});

		xhr.send(formData);
	},
	resetSelection() {
		this.previews.forEach((preview) => URL.revokeObjectURL(preview.url));
		this.files = [];
		this.previews = [];
		this.dragIndex = null;
		this.errorMessage = '';
		this.submitError = '';
		this.resetSubmissionState();
		this.$refs.input.value = '';
	},
});

Alpine.start();

function showFavoriteFeedback(message) {
	if (!feedbackElement) {
		return;
	}

	feedbackElement.textContent = message;
	feedbackElement.classList.add('is-visible');

	window.clearTimeout(feedbackElement._hideTimeout);
	feedbackElement._hideTimeout = window.setTimeout(() => {
		feedbackElement.classList.remove('is-visible');
	}, 2000);
}

function isFavoriteStateOn(button) {
	return button.dataset.favorited === 'true' || button.dataset.favorited === '1';
}

function getFavoriteButtons(attractionId) {
	return Array.from(document.querySelectorAll(`.favorite-toggle[data-attraction-id="${attractionId}"]`));
}

function getFavoriteCountNodes(attractionId) {
	return Array.from(document.querySelectorAll(`[data-favorites-count][data-attraction-id="${attractionId}"]`));
}

function getCurrentFavoriteCount(attractionId) {
	const firstNode = getFavoriteCountNodes(attractionId)[0];

	if (!firstNode) {
		return 0;
	}

	return Number(firstNode.dataset.favoritesCount || firstNode.textContent || 0);
}

function animateFavoriteCount(node) {
	node.classList.remove('is-bumping');
	void node.offsetWidth;
	node.classList.add('is-bumping');
}

function renderFavoriteCount(attractionId, nextCount) {
	const safeCount = Math.max(0, Number(nextCount) || 0);

	getFavoriteCountNodes(attractionId).forEach((node) => {
		node.dataset.favoritesCount = String(safeCount);

		if (node.dataset.favoritesFormat === 'inline') {
			node.textContent = `${safeCount} saved`;
		} else {
			node.textContent = String(safeCount);
		}

		animateFavoriteCount(node);
	});
}

function renderFavoriteButton(button, isFavorited, favoriteId = '') {
	button.dataset.favorited = isFavorited ? 'true' : 'false';
	button.dataset.favoriteId = favoriteId ? String(favoriteId) : '';
	button.classList.toggle('is-active', isFavorited);
	button.setAttribute('aria-pressed', isFavorited ? 'true' : 'false');

	const style = button.dataset.favoriteStyle;

	if (style === 'button') {
		button.classList.toggle('btn-primary', isFavorited);
		button.classList.toggle('btn-outline', !isFavorited);
		button.textContent = isFavorited ? 'Saved to Favorites' : 'Add to Favorites';
		button.setAttribute('aria-label', isFavorited ? 'Remove from favorites' : 'Add to favorites');
	}

	if (style === 'icon') {
		button.textContent = isFavorited ? '♥' : '♡';
		button.setAttribute('aria-label', isFavorited ? 'Remove from favorites' : 'Add to favorites');
	}
}

function setLoadingState(button, isLoading) {
	button.disabled = isLoading;
	button.classList.toggle('is-loading', isLoading);
}

function setFavoriteLoadingState(attractionId, isLoading) {
	getFavoriteButtons(attractionId).forEach((button) => setLoadingState(button, isLoading));
}

function animateFavoriteButton(button) {
	button.classList.remove('animate-pop');
	button.classList.remove('is-pulsing');
	// Force reflow so repeated clicks replay animation.
	void button.offsetWidth;
	button.classList.add('animate-pop');
	button.classList.add('is-pulsing');

	window.setTimeout(() => {
		button.classList.remove('is-pulsing');
	}, 600);
}

async function toggleFavorite(button) {
	const endpoint = button.dataset.favoriteEndpoint;
	const attractionId = button.dataset.attractionId;
	const favoriteId = button.dataset.favoriteId;
	const isFavorited = isFavoriteStateOn(button);

	if (!endpoint || !attractionId || !csrfToken) {
		return;
	}

	if (isFavorited && !favoriteId) {
		showFavoriteFeedback('Unable to update favorite state right now');
		return;
	}

	setFavoriteLoadingState(attractionId, true);

	try {
		const response = await fetch(isFavorited ? `${endpoint}/${favoriteId}` : endpoint, {
			method: isFavorited ? 'DELETE' : 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken,
				Accept: 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
			},
			body: isFavorited ? undefined : JSON.stringify({ attraction_id: Number(attractionId) }),
		});

		const responseText = await response.text();
		let payload = {};

		if (responseText) {
			try {
				payload = JSON.parse(responseText);
			} catch {
				payload = {};
			}
		}

		if (!response.ok) {
			throw new Error(payload.message || 'Unable to update favorite state');
		}

		const nextFavoriteId = payload.favorite_id || '';
		const currentCount = getCurrentFavoriteCount(attractionId);
		const nextCount = Number.isFinite(Number(payload.favorites_count))
			? Number(payload.favorites_count)
			: Math.max(0, currentCount + (isFavorited ? -1 : 1));

		getFavoriteButtons(attractionId).forEach((item) => {
			renderFavoriteButton(item, !isFavorited, !isFavorited ? nextFavoriteId : '');
			animateFavoriteButton(item);
		});
		renderFavoriteCount(attractionId, nextCount);

		showFavoriteFeedback(isFavorited ? 'Removed from favorites' : 'Added to favorites ❤️');
	} catch (error) {
		showFavoriteFeedback(error.message || 'Unable to update favorites right now');
	} finally {
		setFavoriteLoadingState(attractionId, false);
	}
}

document.addEventListener('click', (event) => {
	const button = event.target.closest('.favorite-toggle[data-favorite-endpoint]');

	if (!button) {
		return;
	}

	event.preventDefault();
	event.stopPropagation();
	toggleFavorite(button);
});

async function deleteAdminGalleryItem(button) {
	const deleteUrl = button.dataset.deleteUrl;
	const label = button.dataset.galleryLabel || 'this item';

	if (!deleteUrl || !csrfToken) {
		window.alert('Unable to delete item right now.');
		return;
	}

	if (!confirm(`Are you sure you want to delete ${label}?`)) {
		return;
	}

	button.disabled = true;

	try {
		const response = await fetch(deleteUrl, {
			method: 'DELETE',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'X-CSRF-TOKEN': csrfToken,
				'Accept': 'application/json',
			},
		});

		if (!response.ok) {
			throw new Error('Failed to delete item');
		}

		// Find and remove the item from the gallery
		const item = button.closest('[data-gallery-item]');
		if (item) {
			item.remove();
		}
	} catch (error) {
		button.disabled = false;
		window.alert(error.message || 'Unable to delete this item right now.');
	}
}

document.addEventListener('click', (event) => {
	const deleteButton = event.target.closest('[data-gallery-delete]');

	if (!deleteButton) {
		return;
	}

	event.preventDefault();
	event.stopPropagation();
	deleteAdminGalleryItem(deleteButton);
});

function renderReviewStars(starButtons, rating, mode = 'selected') {
	starButtons.forEach((button) => {
		const value = Number(button.dataset.starValue || 0);
		const isOn = value <= rating;
		button.classList.toggle('is-active', mode === 'selected' && isOn);
		button.classList.toggle('is-hovered', mode === 'hovered' && isOn);
		button.setAttribute('aria-checked', mode === 'selected' && value === rating ? 'true' : 'false');
	});
}

function animateReviewStars(starButtons, rating) {
	starButtons.forEach((button) => {
		const value = Number(button.dataset.starValue || 0);

		if (value > rating) {
			return;
		}

		button.classList.remove('is-popping');
		void button.offsetWidth;
		button.classList.add('is-popping');
	});
}

function applyOptimisticRatingStats(form, nextRating) {
	const averageNode = document.querySelector('[data-rating-average]');
	const countNode = document.querySelector('[data-rating-count]');
	const totalReviewsNode = document.querySelector('[data-total-reviews]');

	if (!averageNode || !countNode || !totalReviewsNode) {
		return;
	}

	const hadReview = form.dataset.hadReview === 'true';
	const previousRating = Number(form.dataset.previousRating || 0);
	const currentCount = Number((countNode.textContent || '').match(/\d+/)?.[0] || 0);
	const currentAverage = Number((averageNode.textContent || '').replace(/[^\d.]/g, '') || 0);

	const nextCount = hadReview ? currentCount : currentCount + 1;

	if (nextCount <= 0) {
		return;
	}

	const currentSum = currentAverage * currentCount;
	const nextSum = hadReview
		? currentSum - previousRating + nextRating
		: currentSum + nextRating;
	const nextAverage = nextSum / nextCount;

	averageNode.textContent = `★ ${nextAverage.toFixed(1)}`;
	countNode.textContent = `${nextCount} ratings`;
	totalReviewsNode.textContent = `${nextCount} total reviews`;

	form.dataset.hadReview = 'true';
	form.dataset.previousRating = String(nextRating);
}

function showReviewFeedback(form, message, isError = false) {
	let feedback = form.querySelector('[data-review-feedback]');

	if (!feedback) {
		feedback = document.createElement('p');
		feedback.setAttribute('data-review-feedback', '1');
		feedback.className = 'review-feedback';
		form.appendChild(feedback);
	}

	feedback.textContent = message;
	feedback.classList.toggle('is-error', isError);
	feedback.classList.add('is-visible');

	window.clearTimeout(feedback._hideTimeout);
	feedback._hideTimeout = window.setTimeout(() => {
		feedback.classList.remove('is-visible');
	}, 2200);
}

async function submitReviewForm(form) {
	const reviewUrl = form.dataset.reviewUrl;
	const attractionId = form.dataset.attractionId;
	const currentUserId = Number(form.dataset.currentUserId || 0);
	const ratingField = form.querySelector('[data-review-rating]');
	const csrfField = form.querySelector('input[name="_token"]');
	const commentField = form.querySelector('textarea[name="comment"]');
	const submitButton = form.querySelector('button[type="submit"]');
	const reviewList = document.querySelector('[data-review-list]');

	if (!reviewUrl || !attractionId || !csrfToken || !ratingField || !submitButton) {
		return;
	}

	const rating = Number(ratingField.value || 0);

	if (rating < 1 || rating > 5) {
		showReviewFeedback(form, 'Please choose a rating from 1 to 5 stars.', true);
		return;
	}

	setLoadingState(submitButton, true);

	try {
		const resolvedReviewUrl = (() => {
			try {
				const parsedUrl = new URL(String(reviewUrl), window.location.origin);

				if (parsedUrl.origin !== window.location.origin) {
					return `${parsedUrl.pathname}${parsedUrl.search}`;
				}

				return parsedUrl.toString();
			} catch {
				return reviewUrl;
			}
		})();

		const requestBody = new URLSearchParams();
		requestBody.set('_token', String(csrfField?.value || csrfToken || ''));
		requestBody.set('attraction_id', String(Number(attractionId)));
		requestBody.set('rating', String(rating));
		requestBody.set('comment', commentField ? commentField.value.trim() : '');

		const response = await fetch(resolvedReviewUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
				'X-CSRF-TOKEN': csrfField?.value || csrfToken || '',
				Accept: 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
			},
			body: requestBody.toString(),
		});

		if (response.redirected) {
			window.location.href = response.url;
			return;
		}

		let payload = {};

		try {
			payload = await response.json();
		} catch {
			payload = {};
		}

		if (!response.ok) {
			if ([401, 403, 419].includes(response.status)) {
				form.submit();
				return;
			}

			const firstValidationError = payload?.errors
				? Object.values(payload.errors).flat()[0]
				: null;

			throw new Error(firstValidationError || payload.message || 'Unable to save your review right now.');
		}

		showReviewFeedback(form, payload.message || 'Review saved successfully.');
		applyOptimisticRatingStats(form, rating);

		if (reviewList && payload.data) {
			const review = payload.data;
			const existingItem = reviewList.querySelector(`[data-review-user-id="${currentUserId}"]`);
			const reviewMarkup = `
				<article class="review-item" data-review-item data-review-user-id="${currentUserId}">
					<div class="row-between">
						<p class="review-user">${review.user?.name ?? 'You'}</p>
						<p class="review-stars">${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}</p>
					</div>
					${review.comment ? `<p class="review-comment-text">${review.comment}</p>` : ''}
				</article>
			`;

			if (existingItem) {
				existingItem.outerHTML = reviewMarkup;
			} else {
				reviewList.insertAdjacentHTML('afterbegin', reviewMarkup);
			}
		}

		if (attractionId) {
			try {
				const attractionResponse = await fetch(`/api/v1/attractions/${attractionId}`, {
					headers: {
						Accept: 'application/json',
						'X-Requested-With': 'XMLHttpRequest',
					},
				});

				if (attractionResponse.ok) {
					const attractionPayload = await attractionResponse.json();
					const averageNode = document.querySelector('[data-rating-average]');
					const countNode = document.querySelector('[data-rating-count]');
					const totalReviewsNode = document.querySelector('[data-total-reviews]');

					if (averageNode) {
						averageNode.textContent = `★ ${Number(attractionPayload.data?.average_rating ?? 0).toFixed(1)}`;
					}

					if (countNode) {
						countNode.textContent = `${attractionPayload.data?.reviews_count ?? 0} ratings`;
					}

					if (totalReviewsNode) {
						totalReviewsNode.textContent = `${attractionPayload.data?.reviews_count ?? 0} total reviews`;
					}
				}
			} catch {
				// Keep UI responsive even if stats refresh fails.
			}
		}
	} catch (error) {
		showReviewFeedback(form, error.message || 'Unable to save your review right now.', true);
	} finally {
		setLoadingState(submitButton, false);
	}
}

document.querySelectorAll('[data-review-form]').forEach((form) => {
	const ratingField = form.querySelector('[data-review-rating]');
	const starButtons = Array.from(form.querySelectorAll('[data-star-value]'));

	if (!ratingField || !starButtons.length) {
		return;
	}

	let selectedRating = Number(ratingField.value || 0);
	renderReviewStars(starButtons, selectedRating);

	starButtons.forEach((button) => {
		const value = Number(button.dataset.starValue || 0);

		button.addEventListener('mouseenter', () => renderReviewStars(starButtons, value, 'hovered'));
		button.addEventListener('click', () => {
			selectedRating = value;
			ratingField.value = String(value);
			renderReviewStars(starButtons, selectedRating);
			animateReviewStars(starButtons, selectedRating);
		});

		button.addEventListener('keydown', (event) => {
			if (!['ArrowRight', 'ArrowLeft', 'ArrowUp', 'ArrowDown'].includes(event.key)) {
				return;
			}

			event.preventDefault();

			if (event.key === 'ArrowRight' || event.key === 'ArrowUp') {
				selectedRating = Math.min(5, selectedRating + 1);
			} else {
				selectedRating = Math.max(1, selectedRating - 1);
			}

			ratingField.value = String(selectedRating);
			renderReviewStars(starButtons, selectedRating);
			animateReviewStars(starButtons, selectedRating);
			starButtons[selectedRating - 1]?.focus();
		});
	});

	ratingField.addEventListener('change', () => {
		selectedRating = Number(ratingField.value || 0);
		renderReviewStars(starButtons, selectedRating);
	});

	const starGroup = form.querySelector('[data-star-group]');
	if (starGroup) {
		starGroup.addEventListener('mouseleave', () => renderReviewStars(starButtons, selectedRating));
	}

	form.addEventListener('submit', (event) => {
		if (typeof window.fetch !== 'function') {
			return;
		}

		event.preventDefault();
		submitReviewForm(form);
	});
});

function initAttractionGallery() {
	const galleryRoot = document.querySelector('[data-attraction-gallery]');

	if (!galleryRoot) {
		return;
	}

	const mainImage = galleryRoot.querySelector('[data-gallery-main-image]');
	const thumbButtons = Array.from(galleryRoot.querySelectorAll('[data-gallery-thumb]'));
	const lightbox = galleryRoot.querySelector('[data-gallery-lightbox]');
	const lightboxImage = galleryRoot.querySelector('[data-gallery-lightbox-image]');
	const lightboxCaption = galleryRoot.querySelector('[data-gallery-caption]');
	const lightboxCounter = galleryRoot.querySelector('[data-gallery-counter]');
	let openButton = galleryRoot.querySelector('[data-gallery-open]');
	const mainStage = galleryRoot.querySelector('.attraction-gallery__main');
	const closeButton = galleryRoot.querySelector('[data-gallery-close]');
	const prevButton = galleryRoot.querySelector('[data-gallery-prev]');
	const nextButton = galleryRoot.querySelector('[data-gallery-next]');
	let closeTimer;
    let stageResetTimer;

	const slides = thumbButtons.length
		? thumbButtons.map((button) => ({
			src: button.dataset.gallerySrc,
			alt: button.dataset.galleryAlt || 'Attraction media',
			type: button.dataset.galleryType || 'image',
		}))
		: [{ 
			src: mainImage?.getAttribute('src') || '', 
			alt: mainImage?.getAttribute('alt') || 'Attraction media',
			type: mainImage?.dataset.mediaType || 'image'
		}];

	let currentIndex = 0;
	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const supportsParallax = window.matchMedia('(hover: hover) and (pointer: fine)').matches && !prefersReducedMotion;

	const updateMainMedia = (slide) => {
		if (!mainStage) return;

		if (slide.type === 'video') {
			mainStage.innerHTML = `
				<div class="gallery-video-player">
					<video class="detail-hero gallery-main-image" data-gallery-main-image data-media-type="video" controls controlsList="nodownload" playsinline preload="auto">
						<source src="${slide.src}" type="video/mp4">
						Your browser does not support the video tag.
					</video>
				</div>
			`;

			const videoElement = mainStage.querySelector('video[data-media-type="video"]');

			if (videoElement) {
				videoElement.currentTime = 0;
				videoElement.play().catch(() => {});
			}
		} else {
			mainStage.innerHTML = `
				<button type="button" class="gallery-main-open" data-gallery-open aria-label="Open media gallery">
					<img class="detail-hero gallery-main-image" src="${slide.src}" alt="${slide.alt}" data-gallery-main-image data-media-type="image">
				</button>
			`;
		}

		openButton = galleryRoot.querySelector('[data-gallery-open]');
	};

	const render = () => {
		const currentSlide = slides[currentIndex];

		if (!currentSlide) {
			return;
		}

		updateMainMedia(currentSlide);

		if (lightboxImage && currentSlide.type === 'image') {
			lightboxImage.src = currentSlide.src;
			lightboxImage.alt = currentSlide.alt;
		}

		if (lightboxCaption) {
			lightboxCaption.textContent = currentSlide.alt;
		}

		if (lightboxCounter) {
			lightboxCounter.textContent = `${currentIndex + 1} / ${slides.length}`;
		}

		thumbButtons.forEach((button, index) => {
			const isActive = index === currentIndex;
			button.classList.toggle('is-active', isActive);
			button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
		});
	};

	const showSlide = (nextIndex) => {
		if (!slides.length) {
			return;
		}

		currentIndex = (nextIndex + slides.length) % slides.length;
		render();
	};

	const openLightbox = () => {
		if (!lightbox) {
			return;
		}

		window.clearTimeout(closeTimer);
		lightbox.hidden = false;
		lightbox.setAttribute('aria-hidden', 'false');
		requestAnimationFrame(() => {
			lightbox.classList.add('is-open');
		});
		document.body.classList.add('gallery-lightbox-open');
	};

	const closeLightbox = () => {
		if (!lightbox) {
			return;
		}

		lightbox.classList.remove('is-open');
		lightbox.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('gallery-lightbox-open');
		closeTimer = window.setTimeout(() => {
			lightbox.hidden = true;
		}, 220);
	};

	thumbButtons.forEach((button, index) => {
		button.addEventListener('click', () => showSlide(index));
	});

	mainStage?.addEventListener('click', (event) => {
		if (event.target.closest('[data-gallery-open]')) {
			openLightbox();
		}
	});
	closeButton?.addEventListener('click', closeLightbox);
	prevButton?.addEventListener('click', () => showSlide(currentIndex - 1));
	nextButton?.addEventListener('click', () => showSlide(currentIndex + 1));

	if (supportsParallax && mainStage) {
		mainStage.addEventListener('mousemove', (event) => {
			const rect = mainStage.getBoundingClientRect();
			const offsetX = ((event.clientX - rect.left) / rect.width - 0.5) * 8;
			const offsetY = ((event.clientY - rect.top) / rect.height - 0.5) * 6;

			mainStage.style.setProperty('--gallery-parallax-x', `${offsetX.toFixed(2)}px`);
			mainStage.style.setProperty('--gallery-parallax-y', `${offsetY.toFixed(2)}px`);
		});

		mainStage.addEventListener('mouseleave', () => {
			window.clearTimeout(stageResetTimer);
			stageResetTimer = window.setTimeout(() => {
				mainStage.style.setProperty('--gallery-parallax-x', '0px');
				mainStage.style.setProperty('--gallery-parallax-y', '0px');
			}, 20);
		});
	}

	lightbox?.addEventListener('click', (event) => {
		if (event.target === lightbox) {
			closeLightbox();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (!lightbox || lightbox.hidden) {
			return;
		}

		if (event.key === 'Escape') {
			closeLightbox();
		}

		if (event.key === 'ArrowLeft') {
			showSlide(currentIndex - 1);
		}

		if (event.key === 'ArrowRight') {
			showSlide(currentIndex + 1);
		}
	});

	render();
}

function initAdminAttractionGalleryReorder() {
	const galleryRoot = document.querySelector('[data-attraction-gallery-admin]');

	if (!galleryRoot) {
		return;
	}

	const saveUrl = galleryRoot.dataset.saveUrl;
	const items = Array.from(galleryRoot.querySelectorAll('[data-gallery-item]'));
	let draggedItem = null;

	if (!saveUrl || !items.length) {
		return;
	}

	const getItemList = () => Array.from(galleryRoot.querySelectorAll('[data-gallery-item]'));

	const persistOrder = async () => {
		const imageIds = getItemList().map((item) => Number(item.dataset.galleryId || 0)).filter(Boolean);

		if (!imageIds.length) {
			return;
		}

		const response = await fetch(saveUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken || '',
				Accept: 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
			},
			body: JSON.stringify({ image_ids: imageIds }),
		});

		if (!response.ok) {
			throw new Error('Unable to save gallery order right now.');
		}
	};

	const renumber = () => {
		getItemList().forEach((item, index) => {
			const label = item.querySelector('.admin-help');

			if (label) {
				label.textContent = `Order #${index + 1}`;
			}
		});
	};

	const handleDropOnItem = async (targetItem, event) => {
		if (!draggedItem || draggedItem === targetItem) {
			return;
		}

		const targetRect = targetItem.getBoundingClientRect();
		const dragIndex = getItemList().indexOf(draggedItem);
		const targetIndex = getItemList().indexOf(targetItem);
		const insertBefore = (event?.clientY ?? 0) < targetRect.top + targetRect.height / 2;

		if (dragIndex < targetIndex && !insertBefore) {
			targetItem.after(draggedItem);
		} else if (dragIndex > targetIndex && insertBefore) {
			targetItem.before(draggedItem);
		} else if (insertBefore) {
			targetItem.before(draggedItem);
		} else {
			targetItem.after(draggedItem);
		}

		renumber();
		await persistOrder();
	};

	items.forEach((item) => {
		item.addEventListener('dragstart', (event) => {
			draggedItem = item;
			event.dataTransfer.effectAllowed = 'move';
			event.dataTransfer.setData('text/plain', item.dataset.galleryId || '');
			item.classList.add('is-dragging');
		});

		item.addEventListener('dragend', () => {
			draggedItem = null;
			item.classList.remove('is-dragging');
			getItemList().forEach((node) => node.classList.remove('is-drop-target'));
		});

		item.addEventListener('dragover', (event) => {
			event.preventDefault();
			item.classList.add('is-drop-target');
		});

		item.addEventListener('dragleave', () => {
			item.classList.remove('is-drop-target');
		});

		item.addEventListener('drop', async (event) => {
			event.preventDefault();
			item.classList.remove('is-drop-target');

			try {
				await handleDropOnItem(item, event);
			} catch (error) {
				window.alert(error.message || 'Unable to save gallery order right now.');
			}
		});
	});

	galleryRoot.addEventListener('dragover', (event) => {
		event.preventDefault();
	});

	galleryRoot.addEventListener('drop', async (event) => {
		if (!draggedItem) {
			return;
		}

		event.preventDefault();

		const lastItem = getItemList().at(-1);

		if (lastItem && draggedItem !== lastItem) {
			lastItem.after(draggedItem);
		}

		renumber();

		try {
			await persistOrder();
		} catch (error) {
			window.alert(error.message || 'Unable to save gallery order right now.');
		}
	});
}

function initHomePageEffects() {
	const homeRoot = document.querySelector('.home-page');

	if (!homeRoot) {
		return;
	}

	const revealItems = Array.from(homeRoot.querySelectorAll('.section-block .card-link, .section-block .section-head'));
	const heroBanner = homeRoot.querySelector('.hero-banner');
	const progressBar = homeRoot.querySelector('[data-hero-progress-bar]');

	if (heroBanner && progressBar) {
		let ticking = false;

		const updateHeroProgress = () => {
			const rect = heroBanner.getBoundingClientRect();
			const consumed = Math.min(Math.max(-rect.top, 0), rect.height);
			const ratio = rect.height > 0 ? consumed / rect.height : 0;
			const progressPercent = `${Math.round(ratio * 100)}%`;

			progressBar.style.setProperty('--hero-scroll-progress', progressPercent);
			progressBar.style.width = progressPercent;
			homeRoot.classList.toggle('is-hero-past', ratio > 0.9);
			ticking = false;
		};

		const onScroll = () => {
			if (ticking) {
				return;
			}

			ticking = true;
			window.requestAnimationFrame(updateHeroProgress);
		};

		updateHeroProgress();
		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll);
	}

	if (!revealItems.length) {
		return;
	}

	revealItems.forEach((item, index) => {
		item.style.opacity = '0';
		item.style.transform = 'translateY(16px)';
		item.style.transition = `opacity 0.45s ease ${(index % 6) * 50}ms, transform 0.45s ease ${(index % 6) * 50}ms`;
	});

	const observer = new IntersectionObserver(
		(entries, currentObserver) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}

				entry.target.style.opacity = '1';
				entry.target.style.transform = 'translateY(0)';
				currentObserver.unobserve(entry.target);
			});
		},
		{ threshold: 0.15 }
	);

	revealItems.forEach((item) => observer.observe(item));
}

initAttractionGallery();
initAdminAttractionGalleryReorder();
initHomePageEffects();
