@php
    $flashType = session('error') || $errors->any() ? 'error' : 'success';
    $flashMessage = $errors->any()
        ? $errors->all()
        : (session('error') ?? session('success'));
@endphp

<div
    id="flashMessageModal"
    class="flash-message-modal"
    role="status"
    aria-live="polite"
    aria-atomic="true"
    hidden
>
    <div class="flash-message-modal__content">
        <div id="flashMessageIcon" class="flash-message-modal__icon" aria-hidden="true"></div>

        <div class="flash-message-modal__text">
            <strong id="flashMessageTitle"></strong>
            <div id="flashMessageText"></div>
        </div>

        <button
            type="button"
            class="flash-message-modal__close"
            aria-label="Dismiss notification"
        >&times;</button>
    </div>
</div>

<style>
    .flash-message-modal {
        position: fixed;
        z-index: 12000;
        top: 1.25rem;
        left: 50%;
        width: min(34rem, calc(100vw - 2rem));
        opacity: 0;
        transform: translate(-50%, -0.75rem);
        transition: opacity 250ms ease, transform 250ms ease;
        pointer-events: none;
    }

    .flash-message-modal.is-visible {
        opacity: 1;
        transform: translate(-50%, 0);
        pointer-events: auto;
    }

    .flash-message-modal__content {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 12px 34px rgba(0, 0, 0, 0.2);
        color: #252525;
    }

    .flash-message-modal--success .flash-message-modal__icon {
        background: #16834a;
    }

    .flash-message-modal--error .flash-message-modal__icon {
        background: #b4232c;
    }

    .flash-message-modal__icon {
        display: grid;
        flex: 0 0 1.75rem;
        width: 1.75rem;
        height: 1.75rem;
        place-items: center;
        border-radius: 50%;
        color: #fff;
        font-weight: 700;
    }

    .flash-message-modal__text {
        flex: 1;
        min-width: 0;
    }

    .flash-message-modal__text strong {
        display: block;
        margin-bottom: 0.2rem;
    }

    .flash-message-modal__text p,
    .flash-message-modal__text ul {
        margin: 0;
    }

    .flash-message-modal__text ul li {
        text-wrap: wrap;
    }

    .flash-message-modal__close {
        border: 0;
        background: transparent;
        color: inherit;
        cursor: pointer;
        font-size: 1.5rem;
        line-height: 1;
    }

    @media (prefers-reduced-motion: reduce) {
        .flash-message-modal {
            transition: none;
        }
    }
</style>

<script>
    (function () {
        const modal = document.getElementById('flashMessageModal');
        const closeButton = modal.querySelector('.flash-message-modal__close');
        const icon = document.getElementById('flashMessageIcon');
        const title = document.getElementById('flashMessageTitle');
        const text = document.getElementById('flashMessageText');
        let dismissTimer;

        function dismiss() {
            modal.classList.remove('is-visible');
            window.setTimeout(function () {
                modal.hidden = true;
            }, 250);
        }

        window.showFlashModal = function (message, type = 'success', customTitle = null) {
            const isError = type === 'error';
            const messages = Array.isArray(message) ? message : [message];
            const list = document.createElement('ul');

            messages.forEach(function (item) {
                const listItem = document.createElement('li');
                listItem.textContent = item;
                list.appendChild(listItem);
            });

            modal.classList.remove('flash-message-modal--success', 'flash-message-modal--error');
            modal.classList.add(isError ? 'flash-message-modal--error' : 'flash-message-modal--success');
            modal.setAttribute('role', isError ? 'alert' : 'status');
            modal.setAttribute('aria-live', isError ? 'assertive' : 'polite');
            icon.textContent = isError ? '!' : '✓';
            title.textContent = customTitle || (isError ? 'Something went wrong' : 'Success');
            text.replaceChildren(list);

            window.clearTimeout(dismissTimer);
            modal.hidden = false;
            window.requestAnimationFrame(function () {
                modal.classList.add('is-visible');
            });

            dismissTimer = window.setTimeout(dismiss, isError ? 15000 : 15000);
        };

        closeButton.addEventListener('click', function () {
            window.clearTimeout(dismissTimer);
            dismiss();
        });

        @if($flashMessage)
            window.showFlashModal(@json($flashMessage), @json($flashType));
        @endif
    })();
</script>
