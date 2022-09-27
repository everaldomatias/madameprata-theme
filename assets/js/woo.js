document.addEventListener('DOMContentLoaded', function() {
    let message = document.querySelector('.p-woo-pagseguro-installment');
    if (message) {
        let priceWithDiscount = priceAndDiscount.price - (priceAndDiscount.price * priceAndDiscount.discount / 100)
        priceWithDiscount = priceWithDiscount.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});

        let newMessage = message.innerHTML
        newMessage = newMessage.replace(" até", "")
        newMessage = newMessage.replace(" - PagSeguro", '<p>Ou <span class="woocommerce-Price-amount amount"><bdi>&nbsp;'+ priceWithDiscount +'</bdi></span> no Pix com <span class="woocommerce-Price-amount amount">10% de desconto</span></p>')
        message.innerHTML = newMessage;
    }
})