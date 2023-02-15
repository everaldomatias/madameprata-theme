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

	// Stock Variable
	let isVariable = document.querySelector('body.woocommerce-type-variable')

	if (isVariable) {
		let divMessage = document.createElement('div')
		divMessage.classList.add('select-variation')

		let spanMessage = document.createElement('span')
		spanMessage.innerText = 'Selecione uma opção para calcular o frete.'
		divMessage.appendChild(spanMessage)
		document.querySelector('.single-product .p-woo-pagseguro-price').appendChild(divMessage)

		let divMessageStock = document.createElement('div')
		divMessageStock.classList.add('product-out-stock')

		let spanMessageStock = document.createElement('span')
		spanMessageStock.innerText = 'Essa opção está fora de estoque'
		divMessageStock.appendChild(spanMessageStock)
		document.querySelector('.single-product .mfn-variations-wrapper').appendChild(divMessageStock)


		let selectVariations = document.querySelector('.mfn-variations-wrapper select')
		let productVariations = window.productStock
		let messageSelectVariation = document.querySelector('.single-product .select-variation')
		let messageOutStock = document.querySelector('.single-product .product-out-stock')
		let cartButton = document.querySelector('.single-product button.single_add_to_cart_button')
		let variationWrap = document.querySelector('.single-product .single_variation_wrap')

		if (selectVariations && productVariations) {

			// Load
			if (selectVariations.options[selectVariations.selectedIndex].value == 0) { // Nenhuma variacao selecionada no load
				messageSelectVariation.style.display = 'block'
				variationWrap.style.display = 'none'
				cartButton.style.display = 'none'
			} else {

				if ( productVariations.stock[selectVariations.options[selectVariations.selectedIndex].value] > 0 ) {
					messageSelectVariation.style.display = 'none'
					variationWrap.style.display = 'block'
					cartButton.style.display = 'block'
				} else {
					messageOutStock.style.display = 'inline-block'
				}
			}

			selectVariations.addEventListener('change', (event) => {
				let selected = event.target.value

				messageSelectVariation.style.display = 'none'
				variationWrap.style.display = 'none'
				cartButton.style.display = 'none'
				messageOutStock.style.display = 'none'

				if (productVariations.stock[selected] == 0 || selected.length === 0) {
					messageSelectVariation.style.display = 'block'
					variationWrap.style.display = 'none'
					cartButton.style.display = 'none'

					if (selected.length === 0) {
						messageOutStock.style.display = 'none'
					} else {
						messageOutStock.style.display = 'inline-block'
					}
				} else {
					messageSelectVariation.style.display = 'none'
					variationWrap.style.display = 'block'
					cartButton.style.display = 'block'
					messageOutStock.style.display = 'none'
				}
			})
		}
	}

	// Stock Simple
	let isSimple = document.querySelector('body.woocommerce-type-simple')

	if (isSimple) {
		console.log('produto simples');
		let divMessageStock = document.createElement('div')
		divMessageStock.classList.add('product-out-stock')

		let spanMessageStock = document.createElement('span')
		spanMessageStock.innerText = 'Esse produto está fora de estoque'
		divMessageStock.appendChild(spanMessageStock)
		document.querySelector('.single-product .price').appendChild(divMessageStock)
	}
})
