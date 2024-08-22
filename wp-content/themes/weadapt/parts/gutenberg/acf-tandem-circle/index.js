const tandemNodes = document.querySelectorAll('.hero-tandem');

if (tandemNodes) {
	tandemNodes.forEach(tandemNode => {
		const svgNode     = tandemNode.querySelector('.hero-tandem__svg');
		const tooltipNode = tandemNode.querySelector('.hero-tandem__tooltip');

		const moveTooltip = (event) => {
			const tooltipElement = event.target.closest('.tooltip');

			if (tooltipElement) {
				const { title } = tooltipElement.dataset;

				tooltipNode.innerHTML = title;

				tooltipNode.classList.add('fixed');

				tooltipNode.style.left = `${event.pageX}px`;
				tooltipNode.style.top = `${event.pageY - tooltipNode.offsetHeight + 30}px`;
			} else {
				tooltipNode.classList.remove('fixed');
			}
		};

		if (svgNode) {
			svgNode.addEventListener('mousemove', moveTooltip);
		}
	});
}