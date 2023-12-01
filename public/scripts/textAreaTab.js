const textarea = document.querySelector(".input-code-snippet")

textarea.addEventListener('keydown', function(e) {
    if(e.key=== "Tab") {
        e.preventDefault();

        textarea.setRangeText(
            '    ',
            textarea.selectionStart,
            textarea.selectionStart,
            'end'
        );
    }
}, false);
