function observePaymentMethods() {
	const paymentMethods = document.querySelectorAll('.wc_payment_method');

	paymentMethods.forEach((methodElement) => {
	  const paymentBox = methodElement.querySelector('.payment_box');
	  console.log(paymentBox);
	  if (paymentBox) {
		// Pega a segunda classe do elemento
		const secondaryClass = paymentBox.className.split(' ').find(cls => cls.startsWith('payment_method_'));

		// Encontra o elemento wc_payment_method_custom correspondente
		const customMethodElement = document.querySelector(`.wc_payment_method_custom[data-target="${secondaryClass}"]`);

		if (customMethodElement) {
			console.log(customMethodElement);
		  // Usa setTimeout para lidar com modificações por JS de terceiros
		  setTimeout(() => {
			const isDisplayed = getComputedStyle(paymentBox).display !== 'none';
			if (isDisplayed) {
			  customMethodElement.classList.add('active');
			} else {
			  customMethodElement.classList.remove('active');
			}
		  }, 0);  // Você pode ajustar o tempo aqui, conforme necessário
		}
	  }
	});
  }

  // Chamada inicial
  observePaymentMethods();

  // Observa futuras mudanças
  // O intervalo pode ser ajustado conforme necessário
  setInterval(observePaymentMethods, 1000);
