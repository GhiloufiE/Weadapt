var zoomTargets = {
    red: { translateXPercent: 50, translateYPercent: 80, scale: 2 }, // translateXPercent: 50, translateYPercent: 80, scale: 2 
    orange: { translateXPercent: 20, translateYPercent: 50, scale: 2 }, // translateXPercent: 20, translateYPercent: 50, scale: 2
    green: { translateXPercent: 50, translateYPercent: 20, scale: 2 }, // translateXPercent: 50, translateYPercent: 20, scale: 2
    blue: { translateXPercent: 80, translateYPercent: 50, scale: 2 }, //  translateXPercent: 80, translateYPercent: 50, scale: 2
    rectangle: { translateXPercent: 50, translateYPercent: 50, scale: 2 } 
};

d3.selectAll('button[data-target]').on('click', function() {
    var targetClass = d3.select(this).attr('data-target');
    var target = zoomTargets[targetClass];

    if (!target) {
        console.error('No zoom target defined for class:', targetClass);
        return;
    }

    var container = d3.select('.diagram-container').node().getBoundingClientRect();
    var translateX = (container.width * (target.translateXPercent / 100) - container.width / 2) * target.scale;
    var translateY = (container.height * (target.translateYPercent / 100) - container.height / 2) * target.scale;

    d3.select('.diagram').transition().duration(1)
        .style('transform', 'translate(' + translateX + 'px,' + translateY + 'px) scale(' + target.scale + ')');
});

d3.select('.zoom-out').on('click', function() {
    d3.select('.diagram').transition().duration(1)
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

        var container = d3.select('.diagram-container').node().getBoundingClientRect();
        var translateX = (container.width * (target.translateXPercent / 100) - container.width / 2) * target.scale;
        var translateY = (container.height * (target.translateYPercent / 100) - container.height / 2) * target.scale;

        d3.select('.diagram').transition().duration(1)
            .style('transform', 'translate(' + translateX + 'px,' + translateY + 'px) scale(' + target.scale + ')');
    });
}

// Initial call to set up handlers
refreshButtonHandlers();
