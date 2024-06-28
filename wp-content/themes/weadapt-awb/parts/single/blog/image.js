document.addEventListener('DOMContentLoaded', function() {
    function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    }

    function addLowQualityWarning(imageLink) {
        const container = document.createElement('div');
        const element = document.createElement('p');
        container.classList.add('low-quality-image-text');
        element.innerText = 'This image is in low quality. Click here to view the high-res version';
        container.appendChild(element);
        imageLink.parentNode.appendChild(container);
    }

    function removeLowQualityWarnings() {
        const warnings = document.querySelectorAll('.low-quality-image-text');
        warnings.forEach(warning => warning.remove());
    }

    const lowQualityEnabled = getCookie('weadapt-low-quality-images') === '1';

    if (lowQualityEnabled) {
        const images = document.querySelectorAll('.wp-block-image a');
        images.forEach(imageLink => addLowQualityWarning(imageLink));
    }

    const checkbox = document.getElementById('low-quality-images');
    checkbox.addEventListener('change', function() {
        document.cookie = 'weadapt-low-quality-images=' + (this.checked ? '1' : '0') + '; path=/';
        removeLowQualityWarnings();
        if (this.checked) {
            const images = document.querySelectorAll('.wp-block-image a');
            images.forEach(imageLink => addLowQualityWarning(imageLink));
        }
    });
});
