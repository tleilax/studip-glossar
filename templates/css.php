fieldset select[multiple], fieldset input[type=text], fieldset textarea {
    width: 90%;
}
label {
    vertical-align: top;
}

/* Glossar Definitions */
dl.glossar .entries dd {
    background: url(<?= Assets::image_path('kaverl1b.jpg') ?>);
    border: 1px solid #888;
    padding: 1em;
    text-align: justify;
}

/* letters */
.glossar-letters {
    list-style: none;
    margin: 0;
    padding: 0;
}
.glossar-letters li {
    color: #888;
    display: inline-block;
    list-style: none;
    margin: 0 0.25em;
    padding: 0;
}
.glossar-letters a {
    color: #000;
    font-weight: bold;
}

/* collapsable forms */
fieldset.collapsable legend {
    background: transparent url(<?= Assets::image_path('icons/16/blue/arr_1down.png') ?>) 2px center no-repeat;
    cursor: pointer;
    padding-left: 20px;
}
fieldset.collapsed {
    border-color: transparent;
}
fieldset.collapsed legend {
    background-image: url(<?= Assets::image_path('icons/16/blue/arr_1right.png') ?>);
}
fieldset.collapsed > div {
    display: none;
}

/* settings form */
form.settings fieldset {
    border: 0;
    margin: 0 0 1em;
    padding: 0;
}
form.settings fieldset > div {
    background: #f3f5f8; /* .steel1 */
    border-bottom: 1px dotted #444;
    overflow: hidden;
    padding: 1em 0.5em 1em 50%;
    position: relative;
    width: 50%;
}
form.settings fieldset > div:nth-child(2n + 1) {
    background: #e7ebf1; /* .steelgraulight */
}
form.settings legend {
    background: #d1d1d1;
    border-bottom: 1px solid #aaa;
    font-weight: bold;
    padding: 4px;
    text-align: center;
    width: 100%;
}
form.settings label {
    background: #fff;
    display: inline-block;
    left: 0;
    padding: 1em 0.5em 2em;
    position: absolute;
    text-align: right;
    top: 0;
    width: 48%;
}
form.settings label small {
    color: #444;
    display: none;
}
form.settings fieldset > div:hover label small {
    display: block;
}
.settings .type-button {
    text-align: center;
}

/* collapsable definition lists */
dl.collapsable dt {
    background: transparent url(<?= Assets::image_path('icons/16/blue/arr_1down.png') ?>) 2px center no-repeat;
    cursor: pointer;
    padding-left: 20px;
}
dl.collapsable dt.collapsed {
    background-image: url(<?= Assets::image_path('icons/16/blue/arr_1right.png') ?>);   
}
dl.collapsable dt.collapsed + dd {
    display: none;
}

/* pagination */
.pagination {
    text-align: center;
}
.pagination img {
    vertical-align: top;
}
