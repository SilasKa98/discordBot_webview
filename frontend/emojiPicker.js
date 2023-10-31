
function handleEmojiPicker(elem){
    const picker = elem.nextElementSibling;
    if(picker.style.display == "none"){
        picker.style.display = "block";
    }else{
        picker.style.display = "none";
    }


     // Event Listener für das emoji-click-Event hinzufügen -> einfügen in input feld
    picker.addEventListener('emoji-click', (event) => {
        let emojiPickerTarget = event.target.previousElementSibling.previousElementSibling;
        const selectedEmoji = event.detail.emoji;
        emojiPickerTarget.value = selectedEmoji.unicode;
        picker.style.display = "none";
        //document.getElementById("selectedEmoji").value = selectedEmoji.unicode;
    });

    picker.addEventListener("blur", (event) => {
        picker.style.display = "none";
    });

    // Adjust twemoji styles
    const style = document.createElement('style')
    style.textContent = `.twemoji {
        width: var(--emoji-size);
        height: var(--emoji-size);
        pointer-events: none;
    }`
    picker.shadowRoot.appendChild(style)

    const observer = new MutationObserver(() => {
        for (const emoji of picker.shadowRoot.querySelectorAll('.emoji')) {
            // Avoid infinite loops of MutationObserver
            if (!emoji.querySelector('.twemoji')) {
            // Do not use default 'emoji' class name because it conflicts with emoji-picker-element's
            twemoji.parse(emoji, { className: 'twemoji' })
            //console.log(twemoji);

            }
        }
    })
    observer.observe(picker.shadowRoot, {
        subtree: true,
        childList: true
    })
}

console.log("fooBar");