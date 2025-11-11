document.addEventListener('DOMContentLoaded', function () {
    const header = document.getElementById('mainHeader');
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Mobile menu toggle
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function (e) {
            e.stopPropagation();
            const isHidden = mobileMenu.classList.contains('hidden');

            if (isHidden) {
                mobileMenu.classList.remove('hidden');
                this.innerHTML = '<i class="fas fa-times text-xl"></i>';
                this.classList.add('text-blue-600');
            } else {
                mobileMenu.classList.add('hidden');
                this.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                this.classList.remove('text-blue-600');
            }
        });

         mobileMenu.addEventListener('click', function (e) {
            if (e.target.tagName === 'A') {
                mobileMenu.classList.add('hidden');
                mobileMenuButton.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                mobileMenuButton.classList.remove('text-blue-600');
            }
        });
 
        document.addEventListener('click', function (e) {
            if (!header.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                if (mobileMenuButton) {
                    mobileMenuButton.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                    mobileMenuButton.classList.remove('text-blue-600');
                }
            }
        });
    }

    if (window.scrollY > 100) {
        header.classList.add('scrolled');
    }
});

// Language change function - IMPROVED VERSION
function changeLanguage(lang) {
     changeLanguageWithFetch(lang);
}
 
async function changeLanguageWithFetch(lang) {
    try {
        const response = await fetch(window.AppConfig.routes.languageChange, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.AppConfig.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                locale: lang
            })
        });

        if (response.ok) {
            window.location.reload();
        } else {
            changeLanguageWithForm(lang);
        }
    } catch (error) {
        changeLanguageWithForm(lang);
    }
}

function changeLanguageWithForm(lang) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = window.AppConfig.routes.languageChange;
    form.style.display = 'none';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = window.AppConfig.csrfToken;

    const langInput = document.createElement('input');
    langInput.type = 'hidden';
    langInput.name = 'locale';
    langInput.value = lang;

    form.appendChild(csrfInput);
    form.appendChild(langInput);
    document.body.appendChild(form);
    form.submit();
}

Livewire.on('languageChanged', () => {
    window.location.reload();
});