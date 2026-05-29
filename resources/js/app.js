import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const dictionaryForm = document.querySelector('[data-dictionary-search]');

if (dictionaryForm) {
    const input = dictionaryForm.querySelector('[data-dictionary-input]');
    const results = document.querySelector('[data-dictionary-results]');
    const status = dictionaryForm.querySelector('[data-dictionary-status]');
    const searchUrl = dictionaryForm.dataset.searchUrl;
    let debounceTimer = null;
    let controller = null;

    const setStatus = (message, isLoading = false) => {
        if (!status) {
            return;
        }

        status.innerHTML = message
            ? `<span class="${isLoading ? 'spinner-border spinner-border-sm me-2' : ''}" aria-hidden="true"></span>${message}`
            : '';
    };

    const renderResults = async (url, browserUrl = null) => {
        if (!results) {
            return;
        }

        if (controller) {
            controller.abort();
        }

        controller = new AbortController();
        results.classList.add('is-loading');
        setStatus('Đang tra từ...', true);

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error(`Dictionary search failed with ${response.status}`);
            }

            results.innerHTML = await response.text();
            initLazyLoaders(results);

            if (browserUrl) {
                window.history.replaceState({}, '', browserUrl);
            }

            setStatus('');
        } catch (error) {
            if (error.name !== 'AbortError') {
                setStatus('Không thể tải kết quả. Hãy thử lại.');
            }
        } finally {
            results.classList.remove('is-loading');
        }
    };

    const search = () => {
        const params = new URLSearchParams();
        const query = input.value.trim();

        if (query) {
            params.set('q', query);
        }

        const queryString = params.toString();
        const ajaxUrl = queryString ? `${searchUrl}?${queryString}` : searchUrl;
        const browserUrl = queryString ? `${dictionaryForm.action}?${queryString}` : dictionaryForm.action;

        renderResults(ajaxUrl, browserUrl);
    };

    input?.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(search, 300);
    });

    dictionaryForm.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(debounceTimer);
        search();
    });

    results?.addEventListener('click', (event) => {
        const link = event.target.closest('.dictionary-pagination a');
        if (!link) {
            return;
        }

        event.preventDefault();
        const url = new URL(link.href);
        const ajaxUrl = `${searchUrl}${url.search}`;
        const browserUrl = `${dictionaryForm.action}${url.search}`;
        renderResults(ajaxUrl, browserUrl);
    });
}

const googleTranslateForm = document.querySelector('[data-google-translate]');

if (googleTranslateForm) {
    const input = googleTranslateForm.querySelector('[data-google-translate-input]');
    const result = googleTranslateForm.querySelector('[data-google-translate-result]');
    const status = googleTranslateForm.querySelector('[data-google-translate-status]');
    const translateUrl = googleTranslateForm.dataset.translateUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let debounceTimer = null;
    let controller = null;

    const setTranslateStatus = (message, isLoading = false) => {
        if (!status) {
            return;
        }

        status.innerHTML = message
            ? `<span class="${isLoading ? 'spinner-border spinner-border-sm me-2' : ''}" aria-hidden="true"></span>${message}`
            : '';
    };

    const translate = async () => {
        const text = input?.value.trim() ?? '';

        if (!text) {
            if (result) {
                result.textContent = 'Nhập văn bản để bắt đầu dịch.';
            }
            setTranslateStatus('');
            return;
        }

        if (controller) {
            controller.abort();
        }

        controller = new AbortController();
        googleTranslateForm.classList.add('is-loading');
        setTranslateStatus('Đang dịch...', true);

        try {
            const response = await fetch(translateUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: JSON.stringify({
                    q: text,
                    target: 'vi',
                    source: 'en',
                }),
                signal: controller.signal,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Không thể dịch văn bản.');
            }

            if (result) {
                result.textContent = data.translatedText || '';
            }

            if (data.source) {
                setTranslateStatus(`Nguồn: ${data.source}`);
                return;
            }

            setTranslateStatus(data.detectedSourceLanguage ? `Nguồn: ${data.detectedSourceLanguage}` : '');
        } catch (error) {
            if (error.name !== 'AbortError') {
                if (result) {
                    result.textContent = '';
                }
                setTranslateStatus(error.message || 'Không thể dịch văn bản.');
            }
        } finally {
            googleTranslateForm.classList.remove('is-loading');
        }
    };

    input?.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(translate, 450);
    });
}

const vocabularyForm = document.querySelector('[data-vocabulary-search]');

if (vocabularyForm) {
    const input = vocabularyForm.querySelector('[data-vocabulary-input]');
    const results = document.querySelector('[data-vocabulary-results]');
    const status = vocabularyForm.querySelector('[data-vocabulary-status]');
    const searchUrl = vocabularyForm.dataset.searchUrl;
    let debounceTimer = null;
    let controller = null;

    const setVocabularyStatus = (message, isLoading = false) => {
        if (!status) {
            return;
        }

        status.innerHTML = message
            ? `<span class="${isLoading ? 'spinner-border spinner-border-sm me-2' : ''}" aria-hidden="true"></span>${message}`
            : '';
    };

    const buildVocabularyUrls = () => {
        const params = new URLSearchParams(new FormData(vocabularyForm));

        for (const [key, value] of [...params.entries()]) {
            if (!String(value).trim()) {
                params.delete(key);
            }
        }

        const queryString = params.toString();

        return {
            ajaxUrl: queryString ? `${searchUrl}?${queryString}` : searchUrl,
            browserUrl: queryString ? `${vocabularyForm.action}?${queryString}` : vocabularyForm.action,
        };
    };

    const renderVocabularyResults = async (ajaxUrl, browserUrl = null) => {
        if (!results) {
            return;
        }

        if (controller) {
            controller.abort();
        }

        controller = new AbortController();
        results.classList.add('is-loading');
        setVocabularyStatus('Đang tra từ...', true);

        try {
            const response = await fetch(ajaxUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error(`Vocabulary search failed with ${response.status}`);
            }

            results.innerHTML = await response.text();

            if (browserUrl) {
                window.history.replaceState({}, '', browserUrl);
            }

            setVocabularyStatus('');
        } catch (error) {
            if (error.name !== 'AbortError') {
                setVocabularyStatus('Không thể tải kết quả. Hãy thử lại.');
            }
        } finally {
            results.classList.remove('is-loading');
        }
    };

    const searchVocabulary = () => {
        const urls = buildVocabularyUrls();
        renderVocabularyResults(urls.ajaxUrl, urls.browserUrl);
    };

    input?.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(searchVocabulary, 300);
    });

    vocabularyForm.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(debounceTimer);
        searchVocabulary();
    });

    results?.addEventListener('click', (event) => {
        const link = event.target.closest('.vocabulary-pagination a');
        if (!link) {
            return;
        }

        event.preventDefault();
        const url = new URL(link.href);
        renderVocabularyResults(`${searchUrl}${url.search}`, `${vocabularyForm.action}${url.search}`);
    });
}

document.querySelectorAll('[data-vocabulary-practice-card]').forEach((card) => {
    const input = card.querySelector('[data-vocabulary-practice-input]');
    const checkButton = card.querySelector('[data-vocabulary-practice-check]');
    const feedback = card.querySelector('[data-vocabulary-practice-feedback]');

    const normalizeAnswer = (value) => value
        .trim()
        .toLowerCase()
        .replace(/[\s_-]+/g, ' ');

    const checkAnswer = () => {
        if (!input || !feedback) {
            return;
        }

        const answer = normalizeAnswer(input.dataset.answer || '');
        const userAnswer = normalizeAnswer(input.value);

        card.classList.remove('is-correct', 'is-wrong');

        if (!userAnswer) {
            feedback.textContent = '';
            return;
        }

        if (userAnswer === answer) {
            card.classList.add('is-correct');
            feedback.textContent = 'Đúng. Bạn viết chính xác.';
            return;
        }

        card.classList.add('is-wrong');
        feedback.textContent = `Chưa đúng. Đáp án đúng là: ${input.dataset.answer}`;
    };

    input?.addEventListener('input', () => {
        if (!feedback) {
            return;
        }

        card.classList.remove('is-correct', 'is-wrong');
        feedback.textContent = '';
    });

    input?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            checkAnswer();
        }
    });

    checkButton?.addEventListener('click', checkAnswer);
});

const initLazyLoaders = (root = document) => {
    root.querySelectorAll('[data-lazy-load-more]').forEach((button) => {
        if (button.dataset.lazyBound === 'true') {
            return;
        }

        button.dataset.lazyBound = 'true';

        button.addEventListener('click', async () => {
            const nextUrl = button.dataset.nextUrl;
            const list = button.closest('[data-lazy-list]') || document.querySelector('[data-lazy-list]');

            if (!nextUrl || !list) {
                return;
            }

            button.disabled = true;
            button.textContent = 'Đang tải...';

            try {
                const response = await fetch(nextUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                });

                if (!response.ok) {
                    throw new Error(`Lazy load failed with ${response.status}`);
                }

                const parser = new DOMParser();
                const doc = parser.parseFromString(await response.text(), 'text/html');
                const nextCards = doc.querySelectorAll('.flashcard, .vocabulary-result-item');
                const nextButton = doc.querySelector('[data-lazy-load-more]');
                const pagination = list.querySelector('.vocabulary-pagination');
                const loadMoreWrap = button.closest('.load-more-wrap');

                nextCards.forEach((card) => list.appendChild(card));
                pagination?.remove();
                loadMoreWrap?.remove();

                if (nextButton) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'load-more-wrap';
                    wrapper.appendChild(nextButton);
                    list.after(wrapper);
                    initLazyLoaders(wrapper);
                }
            } catch (error) {
                button.disabled = false;
                button.textContent = 'Không tải được, thử lại';
            }
        });
    });
};

initLazyLoaders();

const initTimedTests = (root = document) => {
root.querySelectorAll('[data-timed-test]').forEach((form) => {
    if (form.dataset.timerBound === 'true') {
        return;
    }

    form.dataset.timerBound = 'true';
    const timer = form.querySelector('[data-test-timer]');
    const status = form.querySelector('[data-test-timer-status]');
    const panel = form.querySelector('[data-test-timer-panel]');
    const submitButton = form.querySelector('button[type="submit"]');
    let remainingSeconds = Number.parseInt(form.dataset.durationSeconds || '0', 10);
    let isSubmitting = false;

    if (!timer || !remainingSeconds || remainingSeconds < 1) {
        return;
    }

    const formatTime = (seconds) => {
        const minutes = Math.floor(seconds / 60);
        const restSeconds = seconds % 60;

        return `${String(minutes).padStart(2, '0')}:${String(restSeconds).padStart(2, '0')}`;
    };

    const submitExpiredTest = () => {
        if (isSubmitting) {
            return;
        }

        isSubmitting = true;

        if (status) {
            status.textContent = 'Hết giờ. Hệ thống đang tự động nộp bài và chấm điểm.';
        }

        if (panel) {
            panel.classList.add('is-expired');
        }

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Đang nộp bài...';
        }

        window.setTimeout(() => form.submit(), 600);
    };

    const tick = () => {
        timer.textContent = formatTime(remainingSeconds);

        if (remainingSeconds <= 60) {
            panel?.classList.add('is-warning');
            if (status) {
                status.textContent = 'Còn dưới 1 phút. Hãy chọn nhanh các câu còn lại.';
            }
        }

        if (remainingSeconds <= 0) {
            window.clearInterval(intervalId);
            submitExpiredTest();
            return;
        }

        remainingSeconds -= 1;
    };

    form.addEventListener('submit', () => {
        isSubmitting = true;
    });

    tick();
    const intervalId = window.setInterval(tick, 1000);
});
};

initTimedTests();

const adminShell = document.querySelector('[data-admin-shell]');

if (adminShell) {
    const sidebar = adminShell.querySelector('.admin-sidebar-nav');
    let workspace = adminShell.querySelector('[data-admin-workspace]');
    let activeController = null;

    const isAdminUrl = (url) => url.origin === window.location.origin && url.pathname.startsWith('/admin');

    const setActiveLink = (url) => {
        sidebar?.querySelectorAll('a').forEach((link) => {
            const linkUrl = new URL(link.href);
            link.classList.toggle('active', linkUrl.pathname === url.pathname);
        });
    };

    const replaceWorkspace = (html, url, shouldPushState = true) => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const nextWorkspace = doc.querySelector('[data-admin-workspace]');

        if (!nextWorkspace || !workspace) {
            window.location.href = url.href;
            return;
        }

        workspace.replaceWith(nextWorkspace);
        workspace = adminShell.querySelector('[data-admin-workspace]');
        document.title = doc.title;
        setActiveLink(url);

        if (shouldPushState) {
            window.history.pushState({ adminUrl: url.href }, doc.title, url.href);
        }
    };

    const loadAdminPage = async (url, shouldPushState = true) => {
        if (!workspace) {
            window.location.href = url.href;
            return;
        }

        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();
        workspace.classList.add('is-loading');

        try {
            const response = await fetch(url.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                signal: activeController.signal,
            });

            if (!response.ok) {
                throw new Error(`Admin page failed with ${response.status}`);
            }

            replaceWorkspace(await response.text(), url, shouldPushState);
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url.href;
            }
        } finally {
            workspace?.classList.remove('is-loading');
        }
    };

    sidebar?.addEventListener('click', (event) => {
        const link = event.target.closest('a');

        if (!link) {
            return;
        }

        const url = new URL(link.href);

        if (!isAdminUrl(url) || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        event.preventDefault();
        loadAdminPage(url);
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);

        if (isAdminUrl(url)) {
            loadAdminPage(url, false);
        }
    });
}

const publicShell = document.querySelector('[data-public-shell]');

if (publicShell) {
    let content = publicShell.querySelector('[data-public-content]');
    const navbar = publicShell.querySelector('.navbar-nav');
    const mainNavbar = publicShell.querySelector('#mainNavbar');
    const menuToggle = publicShell.querySelector('.navbar-toggler');
    let activeController = null;

    if (mainNavbar) {
        mainNavbar.addEventListener('show.bs.collapse', () => document.body.classList.add('mobile-nav-open'));
        mainNavbar.addEventListener('hide.bs.collapse', () => document.body.classList.remove('mobile-nav-open'));
        mainNavbar.addEventListener('hidden.bs.collapse', () => document.body.classList.remove('mobile-nav-open'));
    }

    const closeMobileMenu = () => {
        if (
            !mainNavbar
            || !menuToggle
            || !mainNavbar.classList.contains('show')
            || !window.matchMedia('(max-width: 991.98px)').matches
        ) {
            return;
        }

        menuToggle.click();
    };

    const shouldHandlePublicUrl = (url) => {
        if (url.origin !== window.location.origin) {
            return false;
        }

        if (url.pathname.startsWith('/admin')) {
            return false;
        }

        const ignoredPrefixes = ['/build', '/storage'];

        return !ignoredPrefixes.some((prefix) => url.pathname.startsWith(prefix));
    };

    const syncHead = (doc) => {
        document.title = doc.title;

        const currentDescription = document.querySelector('meta[name="description"]');
        const nextDescription = doc.querySelector('meta[name="description"]');

        if (currentDescription && nextDescription) {
            currentDescription.setAttribute('content', nextDescription.getAttribute('content') || '');
        }

        const currentCanonical = document.querySelector('link[rel="canonical"]');
        const nextCanonical = doc.querySelector('link[rel="canonical"]');

        if (currentCanonical && nextCanonical) {
            currentCanonical.setAttribute('href', nextCanonical.getAttribute('href') || window.location.href);
        }
    };

    const setPublicActiveLink = (url) => {
        navbar?.querySelectorAll('a.nav-link').forEach((link) => {
            const linkUrl = new URL(link.href);
            link.classList.toggle(
                'active',
                linkUrl.pathname === url.pathname || (linkUrl.pathname !== '/' && url.pathname.startsWith(linkUrl.pathname))
            );
        });
    };

    const replacePublicContent = (html, url, shouldPushState = true) => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const nextContent = doc.querySelector('[data-public-content]');

        if (!nextContent || !content) {
            window.location.href = url.href;
            return;
        }

        content.replaceWith(nextContent);
        content = publicShell.querySelector('[data-public-content]');
        syncHead(doc);
        setPublicActiveLink(url);
        initLazyLoaders(content);
        initTimedTests(content);

        if (shouldPushState) {
            window.history.pushState({ publicUrl: url.href }, doc.title, url.href);
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const loadPublicPage = async (url, shouldPushState = true) => {
        if (!content) {
            window.location.href = url.href;
            return;
        }

        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();
        content.classList.add('is-loading');

        try {
            const response = await fetch(url.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                signal: activeController.signal,
            });

            if (!response.ok) {
                throw new Error(`Public page failed with ${response.status}`);
            }

            replacePublicContent(await response.text(), url, shouldPushState);
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url.href;
            }
        } finally {
            content?.classList.remove('is-loading');
        }
    };

    publicShell.addEventListener('click', (event) => {
        const link = event.target.closest('.navbar a[href], .site-footer a[href]');

        if (!link) {
            return;
        }

        const url = new URL(link.href);

        if (
            !shouldHandlePublicUrl(url)
            || event.metaKey
            || event.ctrlKey
            || event.shiftKey
            || event.altKey
            || link.target
            || link.hasAttribute('download')
        ) {
            return;
        }

        event.preventDefault();
        closeMobileMenu();
        loadPublicPage(url);
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);

        if (shouldHandlePublicUrl(url)) {
            loadPublicPage(url, false);
        }
    });
}
