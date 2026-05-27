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
