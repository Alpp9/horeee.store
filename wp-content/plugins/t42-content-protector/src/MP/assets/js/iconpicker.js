/**
 * Content Protector for WordPress. Exclusively on Envato Market: https://1.envato.market/42themeCC
 * @encoding     UTF-8
 * @version      1.0.9
 * @copyright    Copyright (C) 2016 - 2021 42Theme (https://42theme.com). All rights reserved.
 * @license      Envato Standard Licenses
 * @author       Alexander Khmelnitskiy
 * @support      support@42theme.com
 **/

/** The "Upload SVG" button. */
let iconUploadBtns = document.getElementsByClassName( 't42-icon-field-upload-btn' );

for ( const iconUploadBtn of iconUploadBtns ) {
    iconUploadBtn.addEventListener( 'click', ( event ) => {
        event.preventDefault();
        let key_uploader = wp.media( {
            library: {
                type: ['image/svg+xml']
            },
            multiple: false  // Select only one file.
        } ).on('select', function() {
            let attachment = key_uploader.state().get('selection').first().toJSON(); // Selected icon info.
            // noinspection JSUnresolvedVariable
            let iconInput = iconUploadBtn.parentNode.previousElementSibling.querySelector( 'input' ); // Icon input.
            let iconBox = iconUploadBtn.parentNode;
            let previewImg = iconBox.querySelectorAll( '.t42-icon-field-image' )[0]; // Icon preview box.

            /** Set icon id to input. */
            iconInput.value = attachment.id;

            /** Set nice bg. */
            iconBox.classList.add( 't42-with-image' );
            previewImg.innerHTML = '<img src="' + attachment.url + '" class="svg" alt="'+ attachment.alt +'">';
        } )
            .open();

    }, false );
}

/** The "Remove SVG" button. */
let removeIconBtns = document.getElementsByClassName( 't42-icon-field-remove' );

for ( const removeIconBtn of removeIconBtns ) {
    removeIconBtn.addEventListener( 'click', ()=>{
        let iconBox = removeIconBtn.parentNode;
        iconBox.classList.remove( 't42-with-image' );
        iconBox.previousElementSibling.value = '';
    }, false );
}

/** The "Icon Library" button. */
let iconLibBtns = document.getElementsByClassName( 't42-icon-field-library-btn' );
for ( const iconLibBtn of iconLibBtns ) {
    iconLibBtn.addEventListener( 'click', ( event ) => {
        event.preventDefault();

        /** Create a new DOM element from an HTML string. */
        function stringToElement( string ) {
            let parser = new DOMParser(),
                content = 'text/html',
                DOM = parser.parseFromString( string, content );

            /** Return Element. */
            return DOM.body.childNodes[0];
        }

        /** Create Library modal overlay. */
        let libModalBox = document.createElement('div');
        let root = document.getElementsByTagName( 'html' )[0];
        libModalBox.classList.add( 't42-icon-field-modal-box' );

        /** Create modal window. */
        let libModal = stringToElement(`
                    <div>
                        <div class="t42-icon-field-modal-header">
                            <div>Icon Library</div>
                            <span data-t42-icon="icon: close" class="t42-icon"></span>
                        </div>
                        
                        <div class="t42-icon-field-modal-content">
                            <div>
                                <div class="t42-icon-field-icon-search">
                                    <div class="t42-text-field t42-text-field--outlined t42-text-field--with-trailing-icon">
                                        <div class="t42-inline">
                                            <span class="t42-form-icon" t42-icon="icon: search"></span>
                                            <input class="t42-search-field">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="t42-icon-field-category-box">
                                    <div class="t42-icon-field-category-title"><h3>All Icons</h3></div>
                                    <div class="t42-icon-field-category-items">
                                        content
                                    </div>            
                                </div>
                                
                            </div>
                        </div>
                        
                        <div class="t42-icon-field-modal-footer">
                            <div>
                                <button class="t42-button t42-button--dense t42-button--raised t42-ripple-upgraded">
                                    <span class="t42-button__label">Insert</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `);

        /**
         * Close modal.
         **/
        function closeModal() {
            libModalBox.parentNode.removeChild( libModalBox );
            root.classList.remove( 't42-icon-field-modal-opened' );
        }

        /** Close modal by overlay click. */
        libModalBox.addEventListener( 'click', closeModal, false );

        /** Close modal by close button click. */
        libModal.querySelectorAll( '.t42-icon-field-modal-header .t42-icon' )[0].addEventListener( 'click', closeModal, false );

        /** Close modal by Insert button click. */
        libModal.querySelectorAll( '.t42-icon-field-modal-footer .t42-button' )[0].addEventListener( 'click', ( event ) => {

            /** Get selected icon element. */
            let selectedImage = document.querySelectorAll( '.t42-icon-field-category-icon-box.t42-active img' )[0];

            /** Set icon url to input. */
            let input = iconLibBtn.parentNode.previousElementSibling.querySelector( 'input' );
            input.setAttribute( 'value', selectedImage.dataset.icon );

            /** Create a new 'change' event. */
            let eventChange = new Event('change');

            /** Dispatch it. */
            input.dispatchEvent( eventChange );

            /** Update icon preview. */
            iconLibBtn.parentNode.classList.add( 't42-with-image' );
            iconLibBtn.parentNode.querySelectorAll( '.t42-icon-field-image' )[0].innerHTML='<img src="' + selectedImage.getAttribute( 'src' ) + '" class="svg" alt="' + selectedImage.getAttribute( 'alt' ) + '">' ;

            closeModal();
        }, false );

        /**
         * Do not close modal by click on window
         * and stop event propagation to overlay.
         **/
        libModal.addEventListener( 'click', ( event ) => { event.stopPropagation(); }, false );

        /** Get icon library data. */
        let iLibrary = JSON.parse( iconLibBtn.dataset.library );
        let iFolder = iconLibBtn.dataset.folder;
        let iconsHTML = '';
        for ( const iconCategory of iLibrary ) {
            iconsHTML += '<div class="t42-icon-field-category-title"><h3>' + iconCategory.category_name + '</h3></div>';

            iconsHTML += '<div class="t42-icon-field-category-items">';

            let icons = iconCategory.icons;
            for ( const icon of icons ) {
                iconsHTML += '<div class="t42-icon-field-category-icon-box" data-filter="' + icon.keywords.toString() + '">';
                iconsHTML +=    '<div>';
                iconsHTML +=        '<img src="' + iFolder + icon.path + '" data-icon="' + icon.path + '" alt="' + icon.name + '" loading="lazy" />';
                iconsHTML +=        '<span>' + icon.name + '</span>';
                iconsHTML +=    '</div>';
                iconsHTML += '</div>';
            }

            iconsHTML += '</div>';
        }

        let categoryBox = libModal.querySelectorAll( '.t42-icon-field-category-box' )[0];
        categoryBox.innerHTML = iconsHTML;

        let iconBoxes = categoryBox.querySelectorAll( '.t42-icon-field-category-icon-box' );

        function unselectAllIcons() {
            for ( const iconBox of iconBoxes ) {
                iconBox.classList.remove( 't42-active' );
            }
        }

        for ( const iconBox of iconBoxes ) {
            iconBox.addEventListener( 'click', ( event ) => {
                unselectAllIcons();
                iconBox.classList.add( 't42-active' );
            }, false );
        }

        /** Add modal window to overlay. */
        libModalBox.appendChild( libModal );

        /** Show modal. */
        root.classList.add( 't42-icon-field-modal-opened' );
        document.body.appendChild( libModalBox );

        /** Filter icons in Icon Library. */
        let searchInput = document.querySelectorAll( '.t42-icon-field-icon-search .t42-search-field' )[0];
        searchInput.addEventListener( 'input', ( event ) => {
            let icons = document.querySelectorAll( '.t42-icon-field-category-icon-box' );
            for ( const icon of icons ) {
                if ( ! icon.dataset.filter.includes( searchInput.value ) ) {
                    icon.style.display = 'none';
                } else {
                    icon.style.display = '';
                }
            }
        }, false );

    }, false );
}