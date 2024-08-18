var zoomTargets = {
    red: { element: '.tandem-first-circle', scale: 2 },
    orange: { element: '.tandem-second-circle', scale: 2 },
    green: { element: '.tandem-third-circle', scale: 2 },
    blue: { element: '.tandem-fourth-circle', scale: 2 },
    rectangle: { element: '.tandem-middle-content-rectangle', scale: 2 }
};

function calculateTransform(targetClass) {
    var target = zoomTargets[targetClass];
    if (!target) {
        console.error('No zoom target defined for class:', targetClass);
        return null;
    }

    var container = d3.select('.tandem-first-content').node().getBoundingClientRect();
    var targetElement = d3.select(target.element).node().getBoundingClientRect();

    // Calculate the center of the container
    var containerCenterX = container.left + container.width / 2;
    var containerCenterY = container.top + container.height / 2;

    // Calculate the center of the target element
    var targetCenterX = targetElement.left + targetElement.width / 2;
    var targetCenterY = targetElement.top + targetElement.height / 2;

    // Calculate translation needed to center the target element in the container
    var translateX = containerCenterX - targetCenterX;
    var translateY = containerCenterY - targetCenterY;

    return { translateX, translateY, scale: target.scale };
}

function applyTransform(transform) {
    // Apply the calculated transform
    d3.select('.tandem-first-content').transition().duration(400)
        .style('transform', 'translate(' + transform.translateX + 'px,' + transform.translateY + 'px) scale(' + transform.scale + ')');
}

function zoomToTarget(targetClass) {
    var target = zoomTargets[targetClass];
    if (!target) {
        console.error('No zoom target defined for class:', targetClass);
        return;
    }

    // Force a reflow by accessing the element's offsetHeight
    var targetElement = d3.select(target.element).node();
    if (targetElement) {
        targetElement.offsetHeight;
    }

    var transform = calculateTransform(targetClass);
    if (transform) {
        applyTransform(transform);
    }
}

d3.selectAll('.tandem-nav button[data-target]').on('click', function() {
    var targetClass = d3.select(this).attr('data-target');
    zoomToTarget(targetClass);
});

d3.select('.tandem-container-nav-zoom button').on('click', function() {
    d3.select('.tandem-first-content').transition().duration(500)
        .style('transform', '');
});

function refreshButtonHandlers() {
    d3.selectAll('.tandem-nav button[data-target]').each(function() {
        var targetClass = d3.select(this).attr('data-target');
        if (!zoomTargets[targetClass]) {
            d3.select(this).attr('disabled', 'true');
        } else {
            d3.select(this).on('click', function() {
                zoomToTarget(targetClass);
            });
        }
    });
}

window.addEventListener('resize', function() {
    var currentTransform = d3.select('.tandem-first-content').style('transform');
    if (currentTransform && currentTransform !== 'none') {
        var scaleMatch = currentTransform.match(/scale\(([^)]+)\)/);
        var scale = scaleMatch ? parseFloat(scaleMatch[1]) : 1;

        var targetClass = Object.keys(zoomTargets).find(function(key) {
            return zoomTargets[key].scale === scale;
        });

        if (targetClass) {
            var newTransform = calculateTransform(targetClass);
            if (newTransform) {
                d3.select('.tandem-first-content').style('transform', 'translate(' + newTransform.translateX + 'px,' + newTransform.translateY + 'px) scale(' + newTransform.scale + ')');
            }
        }
    }
});

refreshButtonHandlers();
