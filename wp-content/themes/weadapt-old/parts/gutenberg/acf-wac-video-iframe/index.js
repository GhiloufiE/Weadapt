const apiVideoPlay = (player, playerType) => {
    setTimeout(() => {
        if (playerType === 'youtube') {
            player.setVolume(50);
            player.playVideo();

            return;
        }

        player.setVolume(0.5);
        player.play();
    });
};

const bindVideoEvents = (videoTrigger, videoPlayer, videoParent) => {
    if (videoPlayer.tagName === 'VIDEO') {
        videoTrigger.addEventListener('click', () => {
            videoParent.classList.add('playing');
            videoPlayer.removeAttribute('tabindex');
            videoPlayer.play();
        });

        return;
    }

    const newIframe = videoPlayer.cloneNode();
    videoPlayer.remove();

    const srcIframe = newIframe.src ? newIframe.src : newIframe.dataset.src;

    newIframe.src = `${srcIframe}&playsinline=0&enablejsapi=1`;

    videoParent.append(newIframe);

    let playerType = false;
    let player = false;

    if (newIframe.src.includes('vimeo')) {
        playerType = 'vimeo';

        // eslint-disable-next-line no-undef
        player = new Vimeo.Player(newIframe);
    } else {
        playerType = 'youtube';

        window.YT.ready(() => {
            // eslint-disable-next-line no-undef
            player = new YT.Player(newIframe);
        });
    }

    videoTrigger.addEventListener('click', () => {
        if (player) {
            videoParent.classList.add('playing');
            newIframe.removeAttribute('tabindex');

            apiVideoPlay(player, playerType);
        }
    });
};

const videoInit = () => {
    const videoSections = document.querySelectorAll('.wac-video-iframe');

    videoSections.forEach(video => {
        const videoTrigger = video.querySelector('.wac-video-iframe__video-play');
        const videoPlayer = video.querySelector('iframe') ? video.querySelector('iframe') : video.querySelector('video');
        const videoParent = video.querySelector('.wac-video-iframe__video__container');

        if (videoTrigger && videoPlayer && videoParent) {
            bindVideoEvents(videoTrigger, videoPlayer, videoParent);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    videoInit();
});
