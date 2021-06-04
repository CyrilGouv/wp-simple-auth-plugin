document.addEventListener('DOMContentLoaded', (e) => {
    const showAuthBtn = document.getElementById('cg-show-auth-form')
    const authContainer = document.getElementById('cg-auth-container')
    const close = document.getElementById('cg-auth-close')

    const authForm = document.getElementById('cg-auth-form')
    const status = authForm.querySelector('[data-message="status"]')


    showAuthBtn.addEventListener('click', () => {
        authContainer.classList.add('show')
        showAuthBtn.parentElement.classList.add('hide')
    })


    close.addEventListener('click', () => {
        authContainer.classList.remove('show')
        showAuthBtn.parentElement.classList.remove('hide')
    })


    authForm.addEventListener('submit', (e) => {
        e.preventDefault()

        // Reset the form messages
        resetMessages()

        // Collect all the data
        let data = {
            name: authForm.querySelector('[name="username"]').value,
            password: authForm.querySelector('[name="password"]').value,
            nonce: authForm.querySelector('[name="cg_auth"]').value
        }

        // Validate Everything
        if ( !data.name || !data.password ) {
            status.innerHTML = 'Missing Data'
            status.classList.add('error')
            return
        }

        // Ajax http post request
        const url = authForm.dataset.url
        let params = new URLSearchParams(new FormData(authForm))

        authForm.querySelector('[name="submit"]').value = 'Logging in...'
        authForm.querySelector('[name="submit"]').disabled = true

        fetch(url, {
            method: "POST",
            body: params
        })
        .then(res => res.json())
        .catch(err => {
            resetMessages()
        })
        .then(response => {
            resetMessages()

            if (response === 0 || !response.status) {
                status.innerHTML = response.message
                status.classList.add('error')
                return
            }

            status.innerHTML = response.message
            status.classList.add('success')
            authForm.reset()

            window.location.reload()
        })

    })


    // Reset all messages
    function resetMessages() {
        status.innerHTML = ''
        status.classList.remove('success', 'error')

        authForm.querySelector('[name="submit"]').value = 'Login'
        authForm.querySelector('[name="submit"]').disabled = false
    }
})