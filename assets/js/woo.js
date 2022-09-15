document.addEventListener('DOMContentLoaded', function() {
    let message = document.querySelector('.p-woo-pagseguro-installment');
    if (message) {
        let newMessage = message.innerHTML.split(" ").filter(a=> a != 'até').join(" ")
        message.innerHTML = newMessage;
    }
})