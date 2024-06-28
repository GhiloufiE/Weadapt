const figures = document.querySelectorAll('figure.wp-block-image a');

figures.forEach(figure => {
    const container = document.createElement('div');
    const element = document.createElement('p');
    container.classList.add('low-quality-image-text');
    element.innerText = 'This image is in low quailty click here to view the high res version';
    container.appendChild(element);
    figure.appendChild(container);
})
