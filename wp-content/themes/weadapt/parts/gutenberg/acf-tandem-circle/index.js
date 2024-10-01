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

function initClickableAreas() {
    d3.select('#scope-review').on('click', function() {
        scrollToSection('.tandem-first-content-wrapper', function() {
            zoomToTarget('red'); 
        });
    });

    d3.select('#integrate-knowledge').on('click', function() {
        scrollToSection('.tandem-first-content-wrapper', function() {
            zoomToTarget('blue'); 
        });
    });

    d3.select('#co-design').on('click', function() {
        scrollToSection('.tandem-first-content-wrapper', function() {
            zoomToTarget('green'); 
        });
    });

    d3.select('#co-explore').on('click', function() {
        scrollToSection('.tandem-first-content-wrapper', function() {
            zoomToTarget('orange'); 
        });
    });
}

function scrollToSection(targetSelector, callback) {
    const targetElement = document.querySelector(targetSelector);
    
    if (targetElement) {
        targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start', 
        });


        setTimeout(() => {
            if (typeof callback === 'function') {
                callback();
            }
        }, 300); 
    } else {
        console.error('Target section not found:', targetSelector);
    }
}
	window.onload = function() {
		if (document.querySelector('.inner-circle')) {
			var footer = document.querySelector('.main-footer');
			if (footer) {
				footer.style.paddingTop = '3rem';
			}
		}
	};
	var zoomTargets = {
		red: {
			element: '.tandem-first-circle',
			scale: 3
		},
		orange: {
			element: '.tandem-third-circle',
			scale: 3
		},
		green: {
			element: '.tandem-fourth-circle',
			scale: 3
		},
		blue: {
			element: '.tandem-second-circle',
			scale: 3
		},
		rectangle: {
			element: '.tandem-middle-content-rectangle',
			scale: 3
		}
	};
	var themeUri = '<?php echo get_theme_file_uri('/assets/images/svg/'); ?>';
	var svgMappings = {
		red: themeUri + 'red-selected.svg',
		orange: themeUri + 'orange-selected.svg',
		green: themeUri + 'green-selected.svg',
		blue: themeUri + 'blue-selected.svg',
		rectangle: themeUri + 'gray-selected.svg'
	};


	function getTargetElement(targetClass) {
		var target = zoomTargets[targetClass];
		if (!target) {
			return null;
		}
		return d3.select(target.element).node();
	}

	function calculateTransform(targetClass) {
		var targetElement = getTargetElement(targetClass);
		if (!targetElement) return null;

		var containerElement = d3.select('.tandem-first-content').node();

		var targetRect = targetElement.getBoundingClientRect();
		var containerRect = containerElement.getBoundingClientRect();

		var targetCenterX = targetRect.left + targetRect.width / 2;
		var targetCenterY = targetRect.top + targetRect.height / 2;

		var containerCenterX = containerRect.left + containerRect.width / 2;
		var containerCenterY = containerRect.top + containerRect.height / 2;

		var translateX = containerCenterX - targetCenterX;
		var translateY = containerCenterY - targetCenterY;

		return {
			translateX,
			translateY,
			scale: zoomTargets[targetClass].scale
		};
	}

	function toggleLinks(enable) {
		d3.selectAll('.tandem-inside-red-circle a').attr('tabindex', enable ? 0 : -1);
		d3.selectAll('.tandem-inside-red-circle a').style('pointer-events', enable ? 'auto' : 'none');
	}

	function applyTransform(transform, duration, targetClass) {
		if (transform) {
			d3.select('.tandem-first-content')
				.transition()
				.duration(duration)
				.style('transform', `translate(${transform.translateX}px, ${transform.translateY}px) scale(${transform.scale})`)
				.on('end', function() {
					d3.select(this).style('transform', `translate(${transform.translateX}px, ${transform.translateY}px) scale(${transform.scale})`);
					toggleLinks(transform.scale === zoomTargets[targetClass].scale);
				});
		}
	}


	function addGradientOverlay(targetClass, transform, duration) {
		if (transform) {
			var targetElement = getTargetElement(targetClass);
			if (!targetElement) return;

			var targetRect = targetElement.getBoundingClientRect();
			var containerRect = d3.select('.tandem-first-content').node().getBoundingClientRect();
			var targetCenterX = targetRect.left + targetRect.width / 2 - containerRect.left;
			var targetCenterY = targetRect.top + targetRect.height / 2 - containerRect.top;
			var targetPercentX = (targetCenterX / containerRect.width) * 100;
			var targetPercentY = (targetCenterY / containerRect.height) * 100;
			d3.select('.tandem-first-content')
				.append('div')
				.attr('class', `gradient-overlay ${targetClass}`)
				.style('position', 'absolute')
				.style('top', `0%`)
				.style('left', `0%`)
				.style('width', `100%`)
				.style('height', `100%`)
				.style('background', `radial-gradient(circle at ${targetPercentX}% ${targetPercentY}%, rgba(255, 255, 255, 0) 10%, rgba(255, 255, 255, 1) 40%)`)
				.style('pointer-events', 'none')
				.style('transition', `opacity ${duration}ms`)
				.style('opacity', 0)
				.transition()
				.style('opacity', 1);
		}
	}


	let zoomOrder = ['red', 'orange', 'green', 'blue', 'rectangle']; 
	let currentTargetIndex = -1; 

	window.onload = function() {
  
    initZoomHandlers();
	initClickableAreas();
};

function zoomToTarget(targetClass) {
    if (d3.select('.tandem-first-content').select(`.gradient-overlay.${targetClass}`).node()) {
        return; 
    }
 	 d3.selectAll('.red-content, .orange-content, .green-content, .blue-content, .rectangle-content').style('display', 'none');
 	 d3.selectAll('.red-cards, .orange-cards, .green-cards, .blue-cards, .rectangle-cards').style('display', 'none');
    currentTargetIndex = zoomOrder.indexOf(targetClass);
    if (currentTargetIndex === -1) return;
    d3.select('.arrow-left').style('display', 'block');
    d3.select('.arrow-right').style('display', 'block');
    d3.select('.tandem-first-content')
        .select('.gradient-overlay')
        .transition()
        .duration(0)
        .style('opacity', 0)
        .on('end', function() {
            d3.select(this).remove();
        });

    var lastTransform = calculateTransform(targetClass);
    if (!lastTransform) return;

    applyTransform(lastTransform, 100, targetClass); // Use a longer transition for smoothness

    function handleMutation() {
        lastTransform = calculateTransform(targetClass);
        if (lastTransform) {
            applyTransform(lastTransform, 50, targetClass); // Reapply with a smooth transition
        }
    }

    setTimeout(function() {
        addGradientOverlay(targetClass, lastTransform, 0);
    }, 0);

    var observer = new MutationObserver(handleMutation);
    observer.observe(d3.select('.tandem-first-content').node(), {
        attributes: true,
        childList: true,
        subtree: true
    });
    setTimeout(function() {
        observer.disconnect();
    }, 200);
    d3.select('.tandem-container-nav-zoom button').style('display', 'flex');
    var colors = {
        red: '#B94343',
        orange: '#F6B552',
        green: '#679F5A',
        blue: '#7BC9CC',
        rectangle: '#C5E4FF'
    };

    d3.select('.color-indicator')
        .transition()
        .duration(300)
        .style('background-color', colors[targetClass]);

    d3.selectAll('.tandem-nav button').each(function() {
        var button = d3.select(this);
        var buttonTarget = button.attr('data-target');
        var img = button.select('img.svg-nav');

        if (buttonTarget === targetClass) {
            img.attr('src', svgMappings[buttonTarget]);
        } else {
            var originalSrc = themeUri + buttonTarget + '-nav.svg';
            img.attr('src', originalSrc);
        }
    });
    d3.select('.' + targetClass + '-content').style('display', 'block');
    d3.select('.' + targetClass + '-cards').style('display', 'block');
}



	function resetZoom() {
		d3.select('.arrow-left').style('display', 'none');
		d3.select('.arrow-right').style('display', 'none');
		d3.select('.tandem-first-content')
			.transition()
			.duration(500)
			.style('transform', 'translate(0, 0) scale(1)')
			.on('start', function() {
				// Remove the gradient overlay when resetting zoom
				d3.select(this).select('.gradient-overlay')
					.transition()
					.duration(200)
					.style('opacity', 0)
					.on('end', function() {
						d3.select(this).remove();
					});
			})
			.on('end', function() {
				d3.select(this).style('transform', 'translate(0, 0) scale(1)');
				d3.select('.color-indicator')
					.transition()
					.duration(300)
					.style('background-color', "rgb(197,228,255)");

				d3.selectAll('.tandem-nav button').each(function() {
					var button = d3.select(this);
					var buttonTarget = button.attr('data-target');
					var img = button.select('img.svg-nav');

					var originalSrc = themeUri + buttonTarget + '-nav.svg';
					img.attr('src', originalSrc);
				});

				d3.select('.tandem-container-nav-zoom button').style('display', 'none');
				toggleLinks(false);
			});
	}
	initZoomHandlers();
	toggleLinks(false);


	function handleResize() {
		var currentTransform = d3.select('.tandem-first-content').style('transform');
		if (currentTransform && currentTransform !== 'none') {
			var scaleMatch = currentTransform.match(/scale\(([^)]+)\)/);
			var scale = scaleMatch ? parseFloat(scaleMatch[1]) : 1;

			var targetClass = Object.keys(zoomTargets).find(function(key) {
				return Math.abs(zoomTargets[key].scale - scale) < 0.01;
			});

			if (targetClass) {
				var recalculatedTransform = calculateTransform(targetClass);
				if (recalculatedTransform) {
					applyTransform(recalculatedTransform, 0, targetClass);
				}
			}
		}
	}

	function debounce(func, wait) {
		let timeout;
		return function(...args) {
			const later = () => {
				clearTimeout(timeout);
				func.apply(this, args);
			};
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
		};
	}
	function navigate(direction) {
		if (currentTargetIndex === -1) return; // If no target is set, do nothing
		var newIndex = (currentTargetIndex + direction + zoomOrder.length) % zoomOrder.length;
		var newTarget = zoomOrder[newIndex];
		zoomToTarget(newTarget);
	}
	function initZoomHandlers() {
		d3.selectAll('.tandem-nav button[data-target]').on('click', function() {
			var targetClass = d3.select(this).attr('data-target');
			zoomToTarget(targetClass);
		});
		Object.keys(zoomTargets).forEach(function(targetClass) {
			d3.select(zoomTargets[targetClass].element).on('click', function() {
				zoomToTarget(targetClass);
			});
		});
		d3.select('.tandem-container-nav-zoom button').on('click', resetZoom);
		d3.select('.tandem-container-nav-zoom button').style('display', 'none');
		d3.select('.arrow-right').on('click', function() {
			if (currentTargetIndex < zoomOrder.length - 1) {
				currentTargetIndex++;
				zoomToTarget(zoomOrder[currentTargetIndex]);
			}
		});

		d3.select('.arrow-left').on('click', function() {
			if (currentTargetIndex > 0) {
				currentTargetIndex--;
				zoomToTarget(zoomOrder[currentTargetIndex]);
			}
		});
	}
	initZoomHandlers();