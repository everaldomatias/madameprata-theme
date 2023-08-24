document.addEventListener('DOMContentLoaded', function() {
    let header = document.querySelector('#Action_bar .column,one');
	if (header) {
		const content = "Compre com segurança<br>Qualidade da Prata 925";

		let element = document.createElement("div");
		element.classList.add('selo');
		element.innerHTML = content;
		header.appendChild(element);
	}
})
