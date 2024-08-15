d3.selectAll('button[data-target]').on('click', function() {
    var targetClass = d3.select(this).attr('data-target');
    var targetElement = d3.select('.circle.' + targetClass).node();

    if (!targetElement) {
        console.error('Target element not found for class:', targetClass);
        return;
    }

    // Step 1: Zoom out first
    d3.select('.diagram').transition().duration(500)
        .style('transform', '')
        .on('end', function() { // After zooming out completes, proceed to zoom in

            // Step 2: Calculate the new translation and scale
            var bbox = targetElement.getBoundingClientRect();
            var container = d3.select('.diagram-container').node().getBoundingClientRect();
            var scale = 2; // Zoom scale factor
            var translateX = (container.width / 2 - (bbox.left + bbox.width / 2)) * scale;
            var translateY = (container.height / 2 - (bbox.top + bbox.height / 2)) * scale;

            console.log('Transforming to:', translateX, translateY, 'with scale:', scale);

            // Apply transformation to zoom in to the selected circle
            d3.select('.diagram').transition().duration(500)
                .style('transform', 'translate(' + translateX + 'px,' + translateY + 'px) scale(' + scale + ')');
        });
});

d3.select('.zoom-out').on('click', function() {
    d3.select('.diagram').transition().duration(500)
        .style('transform', '');
});

function refreshButtonHandlers() {
    d3.selectAll('button[data-target]').on('click', function() {
        var targetClass = d3.select(this).attr('data-target');
        var targetElement = d3.select('.circle.' + targetClass).node();

        if (!targetElement) {
            console.error('Target element not found for class:', targetClass);
            return;
        }

        // Step 1: Zoom out first
        d3.select('.diagram').transition().duration(500)
            .style('transform', '')
            .on('end', function() { // After zooming out completes, proceed to zoom in

                // Step 2: Calculate the new translation and scale
                var bbox = targetElement.getBoundingClientRect();
                var container = d3.select('.diagram-container').node().getBoundingClientRect();
                var scale = 2; // Zoom scale factor
                var translateX = (container.width / 2 - (bbox.left + bbox.width / 2)) * scale;
                var translateY = (container.height / 2 - (bbox.top + bbox.height / 2)) * scale;

                console.log('Transforming to:', translateX, translateY, 'with scale:', scale);

                // Apply transformation to zoom in to the selected circle
                d3.select('.diagram').transition().duration(500)
                    .style('transform', 'translate(' + translateX + 'px,' + translateY + 'px) scale(' + scale + ')');
            });
    });
}

// Initial call to set up handlers
refreshButtonHandlers();
