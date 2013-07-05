$(document).ready(function() {

    // Set the Album Artist select "selected".
    var albumArtistSelect = document.getElementById('album-artist-select');
    var albumArtistSelectOptions = $(albumArtistSelect).children();
    var albumArtistIdInput = document.getElementById('artist-id');
    $(albumArtistSelectOptions).each(function() {
        $(this).removeAttr('selected');
        if ($(this).val() === $(albumArtistIdInput).val()) {
            this.selected=true;
        }
    });

    // Set the Song Album select "selected".
    var songAlbumSelect = document.getElementById('song-album-select');
    var songAlbumSelectOptions = $(songAlbumSelect).children();
    var songAlbumIdInput = document.getElementById('album-id');
    $(songAlbumSelectOptions).each(function() {
        $(this).removeAttr('selected');
        if ($(this).val() === $(songAlbumIdInput).val()) {
            this.selected=true;
        }
    });

    // Song edit form.
    var fileUploadsFieldset = document.getElementById('file-uploads-fieldset');
    var fileUploadsFieldsetHtml = $(fileUploadsFieldset).html();
    var mp3PathValue = $('#mp3-path-value').val();
    var oggPathValue = $('#ogg-path-value').val();
    var songFilesHtml = '<legend>Song Files</legend>' +
            mp3PathValue + '<br/>' + oggPathValue +
            '<br/><br/><a id="song-change-button" class="button secondary">Change files</a></fieldset>';
    $(fileUploadsFieldset).html(songFilesHtml);
    var songChangeButton = document.getElementById('song-change-button');
    $(songChangeButton).click(function(event) {
        event.preventDefault();
        $(fileUploadsFieldset).html(fileUploadsFieldsetHtml);
    });

    // Album edit form.
    var artUploadsFieldset = document.getElementById('art-uploads-fieldset');
    var artUploadsFieldsetHtml = $(artUploadsFieldset).html();
    var albumArtUrlValue = $('#album-art-url-value').val();
    var albumArtFilesHtml = '<legend>Album art file</legend>\
            <img src="'+ albumArtUrlValue + '" />\
            <br/><br/><a id="art-change-button" class="button secondary">Change file</a></fieldset>';
    $(artUploadsFieldset).html(albumArtFilesHtml);
    var albumArtChangeButton = document.getElementById('art-change-button');
    $(albumArtChangeButton).click(function(event) {
        event.preventDefault();
        $(artUploadsFieldset).html(artUploadsFieldsetHtml);
    });

});

window.onload = function() {
    // Set the audio volume of the Song view player.
    var songViewAudio = document.getElementById('song-view-audio');
    songViewAudio.volume = 0.5;
};
