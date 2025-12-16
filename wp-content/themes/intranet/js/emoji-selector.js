document.addEventListener('DOMContentLoaded', function() {
    const commentField = document.getElementById('comment');
    if (!commentField) return;

    // Cria botão
    const emojiButton = document.createElement('button');
    emojiButton.innerHTML = '😀';
    emojiButton.classList.add('btn', 'btn-sm', 'btn-emoji');
    emojiButton.type = 'button';
    emojiButton.style.cssText = 'cursor: pointer; font-size: 1.5em;';

    // Cria container principal
    const container = document.createElement('div');
    container.classList.add('emoji-container');

    // Cria wrapper para o picker
    const pickerWrapper = document.createElement('div');
    pickerWrapper.style.cssText = 'position: absolute; bottom: 100%; left: 0; margin-bottom: 10px; z-index: 1000; display: none; width: 350px;';

    // Insere elementos no DOM
    container.appendChild(emojiButton);
    container.appendChild(pickerWrapper);
    
    // Encontra o container do formulário de comentários e insere após o textarea
    const commentForm = commentField.closest('form');
    if (commentForm) {
        const bottomContainer = document.createElement('div');
        bottomContainer.style.cssText = 'display: flex; align-items: center; margin-top: 10px;';
        bottomContainer.appendChild(container);
        commentField.parentNode.insertBefore(bottomContainer, commentField.nextSibling);
    }

    // Variável para controlar se o picker já foi criado
    let pickerCreated = false;

    emojiButton.addEventListener('click', function(e) {
        e.stopPropagation();
        
        // Alterna a visibilidade
        pickerWrapper.style.display = pickerWrapper.style.display === 'none' ? 'block' : 'none';
        
        // Cria o picker apenas na primeira vez
        if (pickerWrapper.style.display === 'block' && !pickerCreated) {
            new EmojiMart.Picker({
                parent: pickerWrapper,
                onEmojiSelect: (emoji) => {
                    commentField.value += emoji.native;
                    pickerWrapper.style.display = 'none';
                    commentField.focus();
                },
                set: 'native',
                theme: 'light',
                locale: 'pt',
                emojiSize: 24,
                perLine: 8,
                previewPosition: 'none',
                dynamicWidth: false,
                exceptEmojis: ['middle_finger']
                
            });
            pickerCreated = true;
        }
    });

    // Encontra o botão de submit
    const submitButton = commentForm.querySelector('input[type="submit"], #submit, .form-submit input[type="submit"]');
    if (submitButton) {
        submitButton.parentNode.insertBefore(container, submitButton);
    }

    // Fecha ao clicar fora
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            pickerWrapper.style.display = 'none';
        }
    });
});