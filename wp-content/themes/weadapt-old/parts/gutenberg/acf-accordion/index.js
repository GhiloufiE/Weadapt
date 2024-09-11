const toggleAccordion = function (event) {
	event.preventDefault();
	const expanded = this.getAttribute('aria-expanded') === 'true' || false;
	this.setAttribute('aria-expanded', !expanded);

	const item = this.nextElementSibling;
	if (!item) return;
	
	item.hidden = !item.hidden;
};

const runAccordions = () => {
	const elements = document.querySelectorAll('.single-accordion__trigger');

	Array.from(elements).forEach(element => {
		element.addEventListener('click', toggleAccordion);
	});
};

runAccordions();

if (window.acf) {
	window.acf.addAction('render_block_preview/type=accordion', runAccordions);
}