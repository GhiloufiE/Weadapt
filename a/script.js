var zoomTargets = {
    red: { translateXPercent: 50, translateYPercent: 20, scale: 2 }, // Example values
    orange: { translateXPercent: 80, translateYPercent: 50, scale: 2 },
    green: { translateXPercent: 50, translateYPercent: 80, scale: 2 },
    blue: { translateXPercent: 20, translateYPercent: 50, scale: 2 },
    rectangle: { translateXPercent: 50, translateYPercent: 50, scale: 2 } // New target for the rectangle
};

d3.selectAll('button[data-target]').on('click', function() {
    var targetClass = d3.select(this).attr('data-target');
    var target = zoomTargets[targetClass];

    if (!target) {
        console.error('No zoom target defined for class:', targetClass);
        return;
    }

    // Calculate the new translation based on container dimensions
    var container = d3.select('.diagram-container').node().getBoundingClientRect();
    var translateX = (container.width * (target.translateXPercent / 100) - container.width / 2) * target.scale;
    var translateY = (container.height * (target.translateYPercent / 100) - container.height / 2) * target.scale;

    console.log('Transforming to:', translateX, translateY, 'with scale:', target.scale);

    // Apply transformation to zoom in to the selected shape
    d3.select('.diagram').transition().duration(300)  // Faster animation
        .style('transform', 'translate(' + translateX + 'px,' + translateY + 'px) scale(' + target.scale + ')');
});

d3.select('.zoom-out').on('click', function() {
    d3.select('.diagram').transition().duration(300)  // Adjusted to match the faster speed
        .style('transform', '');
});

function refreshButtonHandlers() {
    d3.selectAll('button[data-target]').on('click', function() {
        var targetClass = d3.select(this).attr('data-target');
        var target = zoomTargets[targetClass];

        if (!target) {
            console.error('No zoom target defined for class:', targetClass);
            return;
        }

        // Calculate the new translation based on container dimensions
        var container = d3.select('.diagram-container').node().getBoundingClientRect();
        var translateX = (container.width * (target.translateXPercent / 100) - container.width / 2) * target.scale;
        var translateY = (container.height * (target.translateYPercent / 100) - container.height / 2) * target.scale;

        console.log('Transforming to:', translateX, translateY, 'with scale:', target.scale);

        // Apply transformation to zoom in to the selected shape
        d3.select('.diagram').transition().duration(300)  // Faster animation
            .style('transform', 'translate(' + translateX + 'px,' + translateY + 'px) scale(' + target.scale + ')');
    });
}

// Initial call to set up handlers
refreshButtonHandlers();
