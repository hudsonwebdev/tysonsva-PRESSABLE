<?php
/*
 Template Name: Get Around Giude

*/

get_header();

?>
<style>
	.top-banner {
		font-family: "silkabold", "Arial", "Helvetica", sans-serif;
	}
	.explore-head {
		position: relative;
		padding: 0;
	}

	.explore-head .uk-container {
		position: absolute;
		margin: auto;
		top: 30%;
		left: 0;
		right: 0;
	}

	/* main section:nth-child(odd) {
		border-bottom: 2px solid #c00;
	} */


	main section {
		padding: 60px 0;
	}

	section.resources {
		background: #f1f5f8;
		padding: 100px 0 200px;
	}

	h2.script {
		color: #385dff;
		font-size: 75px;
		font-family: "silkabold", "Arial", "Helvetica", sans-serif;
		line-height: 1.25;
	}

	h2.h2ltblu {
		color: #385dff;
		font-weight: normal;
	}

	h3.h3blu {
		color: #0c0042;
	}

	.c-txt {
		text-align: center;
	}

	.tca-button {
		font-size: 13px;
		padding: 5px 10px;
		line-height: 26px;
	}

	.tca-button-wrap.blue .tca-button:before,
	.tca-button-wrap.blue .tca-button:after {
		content: none;

	}

	.explore-head h1 {
		color: #fff;
		font-size: 8vw;
		line-height: 0.8;
		font-family: "silkabold", "Arial", "Helvetica", sans-serif;
		letter-spacing: -0.03em;
		color: #385dff;

	}

	.explore-head h1 span {
		display: block;
		font-size: 4vw;
		margin-left: 1vw;
		letter-spacing: 0;
		color: #fff;

	}

	.explore-head span.wbr {
		text-transform: uppercase;
		font-size: 0.2em;
		margin-left: 7em;
	}

	 .diptych .item {
        margin: 30px;
        padding: 20px 20px 50px 20px;
        background: #385dff;

    }

	@media(min-width:768px){
		.diptych .item {
        width:50%;

    }
	}

	.diptych .item.circulator,
	.diptych .item.metrorail {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: 130px;
	}

	.diptych .item.circulator {
		background-image: url('https://tysonsva.org//wp-content/uploads/2024/05/bus-icon.png');
	}

	.diptych .item.metrorail {
		background-image: url('https://tysonsva.org//wp-content/uploads/2024/05/rail-icon.png');
	}

	.diptych h3 {
		font: 900 45px Roboto, sans-serif;
	}

	.diptych p {
		font: 400 21px Roboto, sans-serif;
	}

	

	.triptych .item {
		position: relative;
		margin: 70px 0 30px;
		padding: 80px 0 0;
		background: #c5eafa;
		display: flex;
		flex-direction: column;
		text-align: center;
	}

	.triptych .item h3 {
		color: #385dff;
		font-size: 52px;
		margin: 0;
	}

	.triptych p {
		padding: 0 20px;
	}

	.triptych figure {
		margin: auto 0 0 0;
		position: relative;
	}

	.triptych .item figure:after {
		content: "";
		display: block;
		box-shadow: inset 0 40px 30px -10px rgba(197, 235, 250, 1);
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		height: 100%;
		width: 100%;
	}



	.triptych .item:before {
		position: absolute;
		margin: auto;
		top: -80px;
		left: 0;
		right: 0;
		width: 150px;
	}

	.triptych .item.bike:before {
	content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='152' height='152'%3E%3Cdefs%3E%3CclipPath id='b'%3E%3Cpath data-name='Path 20' d='M0 54.672h73.37v-77.219H0Z' transform='translate(0 22.547)' fill='none'/%3E%3C/clipPath%3E%3Cfilter id='a' x='0' y='0' width='152' height='152' filterUnits='userSpaceOnUse'%3E%3CfeOffset dx='3' dy='3'/%3E%3CfeGaussianBlur stdDeviation='5' result='blur'/%3E%3CfeFlood flood-opacity='.161'/%3E%3CfeComposite operator='in' in2='blur'/%3E%3CfeComposite in='SourceGraphic'/%3E%3C/filter%3E%3C/defs%3E%3Cg data-name='Group 26'%3E%3Cg transform='translate(0 -.005)' filter='url(%23a)'%3E%3Cg data-name='Ellipse 5' transform='translate(22 22)' fill='%2319a374' stroke='%23fff' stroke-width='10'%3E%3Ccircle cx='51' cy='51' r='51' stroke='none'/%3E%3Ccircle cx='51' cy='51' r='56' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg data-name='Group 35'%3E%3Cg data-name='Group 34' transform='translate(37.315 29.39)' clip-path='url(%23b)'%3E%3Cg data-name='Group 30'%3E%3Cpath data-name='Path 16' d='M60.066 49.094a12.371 12.371 0 0 0-1.64.123L54.15 32.165a7.286 7.286 0 0 1-3.12 2.5l1.22 4.812-4.959.38a9.827 9.827 0 0 1 .144 3.251l-.065.572 2.449-.188-2.826 3.362-.791 6.685 7.377-8.767 1.363 5.415a14.081 14.081 0 0 0-8.182 12.969c0 7.761 5.963 14.062 13.305 14.062 7.326 0 13.305-6.3 13.305-14.062s-5.979-14.062-13.304-14.062m0 24.306a9.988 9.988 0 0 1-9.693-10.244 10.293 10.293 0 0 1 5.5-9.22l1.428 5.678a4.7 4.7 0 0 0-1.593 3.541 4.481 4.481 0 0 0 4.353 4.6 4.615 4.615 0 0 0 .723-9.117l-1.411-5.7c.229-.017.442-.034.688-.034 5.339 0 9.692 4.6 9.692 10.244S65.405 73.4 60.066 73.4' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 31'%3E%3Cpath data-name='Path 17' d='m30.649 58.346.706-5.922-3.5-5.747-5.421-.726a1.791 1.791 0 0 1-.411-.089l-2.919 4.655a12.612 12.612 0 0 0-5.8-1.425c-7.326 0-13.305 6.3-13.305 14.062S5.979 77.22 13.304 77.22c6.716 0 12.285-5.3 13.172-12.155h4.795a8.287 8.287 0 0 1-.918-3.819h-3.876a14.322 14.322 0 0 0-4.37-8.613l2.6-4.1ZM13.305 73.399c-5.339 0-9.692-4.6-9.692-10.244s4.353-10.247 9.692-10.247a9.159 9.159 0 0 1 3.778.819l-3.089 4.897a4.687 4.687 0 0 0-.688-.068 4.489 4.489 0 0 0-4.357 4.599 4.493 4.493 0 0 0 4.353 4.6 4.369 4.369 0 0 0 3.959-2.692h5.565a9.869 9.869 0 0 1-9.524 8.333m9.524-12.151h-5.561a4.971 4.971 0 0 0-.264-.5l3.089-4.9a10.486 10.486 0 0 1 2.74 5.4' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 32'%3E%3Cpath data-name='Path 18' d='m39.083 36.158-6.655-.877 2.716-8.137 4.411 3.01a3.321 3.321 0 0 0 1.428.548c.051.007.1.01.154.014l7.124.6a3.474 3.474 0 0 0 3.668-3.308 3.566 3.566 0 0 0-3.13-3.88l-6.267-.524s-6.63-4.843-7.48-5.175c-.956-.377-4.87-1.832-4.87-1.832a5.161 5.161 0 0 0-4.185.25 5.625 5.625 0 0 0-2.771 3.317l-4.723 14.611a5.865 5.865 0 0 0 .524 4.8 5.267 5.267 0 0 0 3.849 2.6l11.747 1.545a1.446 1.446 0 0 1 1.2 1.63L34 60.806a4 4 0 0 0 3.322 4.507 3.87 3.87 0 0 0 4.267-3.507l2.257-19.179a5.743 5.743 0 0 0-4.764-6.469' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 33'%3E%3Cpath data-name='Path 19' d='M32.749 15.376a7.363 7.363 0 0 0 8.87-5.787A7.819 7.819 0 0 0 36.143.211a7.36 7.36 0 0 0-8.87 5.788 7.819 7.819 0 0 0 5.476 9.377' fill='%23fff'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
	}

	.triptych .item.roll:before { 
	content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='152' height='152'%3E%3Cdefs%3E%3CclipPath id='b'%3E%3Cpath data-name='Rectangle 85' fill='none' d='M0 0h57.307v74.266H0z'/%3E%3C/clipPath%3E%3Cfilter id='a' x='0' y='0' width='152' height='152' filterUnits='userSpaceOnUse'%3E%3CfeOffset dx='3' dy='3'/%3E%3CfeGaussianBlur stdDeviation='5' result='blur'/%3E%3CfeFlood flood-opacity='.161'/%3E%3CfeComposite operator='in' in2='blur'/%3E%3CfeComposite in='SourceGraphic'/%3E%3C/filter%3E%3C/defs%3E%3Cg data-name='Group 114'%3E%3Cg filter='url(%23a)'%3E%3Cg data-name='Ellipse 5' transform='translate(22 22)' fill='%2319a374' stroke='%23fff' stroke-width='10'%3E%3Ccircle cx='51' cy='51' r='51' stroke='none'/%3E%3Ccircle cx='51' cy='51' r='56' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg data-name='Group 113'%3E%3Cg data-name='Group 112' transform='translate(45.293 33.687)' clip-path='url(%23b)'%3E%3Cpath data-name='Path 37' d='m51.269 61-6.361-30.16a3.58 3.58 0 0 0-1.5-5.89l-5.788-1.229-7.756-8.187a5.116 5.116 0 0 0-8.477 2.472l-3.168 16.4L13.631 46.2 2.082 51.558a3.595 3.595 0 1 0 3.026 6.522l12.917-5.989a3.6 3.6 0 0 0 1.875-2.054l2.823-6.854 4.406 8.033-.92 12.284a3.575 3.575 0 0 0 .563 2.208H15.109a6.655 6.655 0 1 0 .018 3.735H37.17a1.871 1.871 0 0 0 1.376-.6l8.618-9.36.434 2.226A6.646 6.646 0 1 0 51.269 61M8.735 70.531a2.913 2.913 0 1 1 2.2-4.819h-2.2a1.8675 1.8675 0 1 0 0 3.735H11a2.9 2.9 0 0 1-2.26 1.084m27.616-4.819H32.82a3.6 3.6 0 0 0 .56-1.672l1.013-13.554a3.625 3.625 0 0 0-.625-2.309L27.6 37.6 29.876 26l3.156 3.335a3.626 3.626 0 0 0 1.5.948l6.668 1.505.092.025 4.985 23.123Zm14.3 4.819a2.919 2.919 0 0 1-2.913-2.916 2.885 2.885 0 0 1 .657-1.835l.422 2.158a1.867 1.867 0 0 0 1.835 1.509 1.62 1.62 0 0 0 .36-.034 1.868 1.868 0 0 0 1.475-2.192l-.422-2.149a2.912 2.912 0 0 1-1.413 5.459M29.334 12.846a6.423 6.423 0 1 0-6.423-6.423 6.423 6.423 0 0 0 6.423 6.423' fill='%23fff'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
	}

	.triptych .item.walk:before {
	content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='152' height='152'%3E%3Cdefs%3E%3CclipPath id='b'%3E%3Cpath data-name='Path 12' d='M0 56.1h45.347v-79.31H0Z' transform='translate(0 23.207)' fill='none'/%3E%3C/clipPath%3E%3Cfilter id='a' x='0' y='0' width='152' height='152' filterUnits='userSpaceOnUse'%3E%3CfeOffset dx='3' dy='3'/%3E%3CfeGaussianBlur stdDeviation='5' result='blur'/%3E%3CfeFlood flood-opacity='.161'/%3E%3CfeComposite operator='in' in2='blur'/%3E%3CfeComposite in='SourceGraphic'/%3E%3C/filter%3E%3C/defs%3E%3Cg data-name='Group 47'%3E%3Cg transform='translate(0 -.005)' filter='url(%23a)'%3E%3Cg data-name='Ellipse 5' transform='translate(22 22)' fill='%2319a374' stroke='%23fff' stroke-width='10'%3E%3Ccircle cx='51' cy='51' r='51' stroke='none'/%3E%3Ccircle cx='51' cy='51' r='56' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg data-name='Group 21'%3E%3Cg data-name='Group 20' transform='translate(50.467 33.35)' clip-path='url(%23b)'%3E%3Cg data-name='Group 17'%3E%3Cpath data-name='Path 9' d='M5.343 73.429a4.473 4.473 0 0 0 2.392 5.615 4.241 4.241 0 0 0 1.418.267 4.1 4.1 0 0 0 3.828-2.847l5.461-15.806-6.329-6.791Z' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 18'%3E%3Cpath data-name='Path 10' d='m42.33 30.873-7.143-1.152a1.416 1.416 0 0 1-.988-.656c-6.012-9.395-7.231-9.894-9.032-10.02l-5.823-.4c-1.114-.079-2.174-.14-10.857 5.359a11.945 11.945 0 0 0-4.32 4.87L.408 36.536a3.929 3.929 0 0 0 1.49 5.123 3.33 3.33 0 0 0 1.644.437 3.522 3.522 0 0 0 3.145-2.03l3.756-7.665a4.555 4.555 0 0 1 1.649-1.863c.267-.171.506-.318.759-.475l-2.529 11.849a7.309 7.309 0 0 0 1.756 6.547l11.38 12.2a.92.92 0 0 1 .229.379l4.928 15.331a4.117 4.117 0 0 0 3.866 2.943 3.639 3.639 0 0 0 1.326-.246 4.42 4.42 0 0 0 2.519-5.54l-4.928-15.334a9.853 9.853 0 0 0-2.163-3.7l-6.38-6.849 4.632-15.615q.4.625.851 1.323a8.535 8.535 0 0 0 5.786 3.875l7.146 1.152a3.579 3.579 0 0 0 4.033-3.189 3.761 3.761 0 0 0-2.977-4.316' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 19'%3E%3Cpath data-name='Path 11' d='M22.501 16.782a8.01 8.01 0 0 0 8.667-7.457A8.3 8.3 0 0 0 24.203.05a8.012 8.012 0 0 0-8.667 7.454 8.3 8.3 0 0 0 6.965 9.278' fill='%23fff'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
	}

	.triptych-brd {
		margin-top: 80px;
	}

	.triptych-brd .item {
		width: 29%;
		text-align: center;
	}

	.triptych-brd .item.wide {
		width: 39%;
		border-left: 1px solid #ccc;
		border-right: 1px solid #ccc;
	}

	.tetraptych h3 {
		color: #0c0042;
		font: 900 38px Roboto, sans-serif;
		text-align: center;
	}


	.tetraptych .item {
		padding: 20px 30px;
		border: 5px solid #385dff
	}


	.tetraptych dl {
		text-align:  left;
	}

	.tetraptych dt {
		color: #04b5df;
		margin-bottom: 10px;
	}

	.tetraptych dd {
		font-size: 18px;
		font-weight: 500;
/* 		margin-left: 1.5em; */
		margin-left: 0;
		margin-bottom: 0.75em;
	}

	.apps {
		display: flex;
		justify-content: space-around;
		flex-wrap: wrap;
	}

	.cstr {
		width: 50%;
		display: flex;
		flex-direction: column;
		text-align: center;
		margin-bottom: 20px;
	}

	.apps img {
		margin: auto auto 10px;
		width: 70px;
	}


	.bg-img {
		width: 100%;
		height: auto;
	}

	
	.col-md-6 {
		width: 48%;
	}

	.outro {
		padding-bottom: 200px;
	}

	

	.d-flex.flex-wrap {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-evenly;
	}

	.post-card {
		padding: 20px;
		width: 100%;
		display: flex;
		flex-direction: column
	}

	@media (min-width: 768px) {
		.post-card {
			width: 50%
		}
	}

	@media (min-width: 992px) {
		.post-card {
			width: 33.3333333333%
		}
	}

	.post-card .border {
		border: 1px solid #ccc !important;
		height: 100%
	}

	.post-card .border .inner {
		border: 15px solid #fff !important;
		background: #fff;
		height: 100%
	}

	.post-card .border .inner .image-wrap {
		position: relative;
		overflow: hidden;
		height: 200px
	}

	.post-card .border .inner .image-wrap img {
		-o-object-fit: cover !important;
		object-fit: cover !important;
		min-height: 100%;
		min-width: 100%
	}

	.post-card .border .inner .image-wrap .label-wrap {
		position: absolute;
		top: 0;
		left: 0
	}

	.post-card .border .inner .image-wrap .label-wrap .content-label {
		font-size: .6rem
	}

	.post-card .border .inner .category-label {
		font-size: .6rem
	}

	.post-card .border .inner .category-label.blog {
		color: #3537f9
	}

	.post-card .border .inner .card-info {
		padding: 15px
	}

	.post-card .border .inner .card-info .top {
		display: flex;
		width: 100%;
		justify-content: space-between;
		font-weight: 700;
		padding-bottom: 10px;
		color: #969696
	}

	.post-card .border .inner .card-info .top .cats {
		font-size: .6rem
	}

	.post-card .border .inner .card-info .top .date {
		font-size: .6rem;
		text-align: right
	}

	.post-card .border .inner .card-info .top .type {
		font-size: .6rem
	}

	.post-card .border .inner .card-info h3 {
		font-size: 32px;
		line-height: 125%;
		margin: 0.5em 0;
	}

	.post-card .border .inner .card-info h3 a {
		transition: color 300ms;
	}

	.post-card .border .inner .card-info h3 a:hover {
		text-decoration: none;
	}

	.acf-gallery {
		text-align: center;
	}
	
	.acf-gallery img {
		max-width: 12%;
		max-height: 150px;
		width: auto;
		height: auto;
		margin: 25px;
	}

	#gform_wrapper_4 .ginput_container input[type="text"],
	#gform_wrapper_4 .ginput_container input[type="email"] {
		border: 1px solid #385dff!important;
		height: 33px!important;
		width: 100%!important;
		margin-bottom: 1em!important;
	}

	#gform_wrapper_4 label.gfield_label,
	#gform_wrapper_4 .gform-field-label,
	#gform_wrapper_4 p.gform_required_legend,
	#gform_wrapper_4 .gfield--type-html {
		color: #385dff!important;
		font-size: 16px!important;
	}

	#gform_wrapper_4.gform-theme--foundation .gform_fields {
		row-gap: 15px!important;
	}

	@media screen and (min-width: 640px) {
		.tetraptych,
		.triptych,
		.diptych,
		.triptych-brd,
		.signup .row {
			display: flex;
			justify-content: space-between;
		}

		.triptych .item {
			width: 32%;
			
		}

		.tetraptych .item {
			width: 24%;
		}

		img.alignleft {
			margin: 1.5em 1.5em 0 0;
			float: left;
		}
	}
</style>
	<main id="primary" class="site-main">
	
			<?php
			while ( have_posts() ) :
				the_post();

				the_content();

			endwhile; // End of the loop.
			?>
						
			<div class="page-header news-header">
				<div class="uk-container">
					<div class="page-header-text">
						<p id="breadcrumbs">
							<span>
								
								<span>
									<a href="/exploretysons">Explore Tysons</a>
								</span> / 
								<span class="breadcrumb_last" aria-current="page">Get Around Guide</span>
							</span>
						</p>
						<h1>Tysons Get Around Guide</h1>
					</div>
				</div>
				<div class="page-header-graphic">
					<svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 805 932">
					<!-- Generator: Adobe Illustrator 29.2.1, SVG Export Plug-In . SVG Version: 2.1.0 Build 116)  -->
					<defs>
					<style>
					.news0 {
					fill: #260d66;
					}

					.news1 {
					fill: #00b852;
					}

					.news2 {
					fill: #9adcf7;
					}
					</style>
					</defs>
					<path class="news1" d="M0,262v174l174-174H0Z"></path>
					<path class="news0" d="M271.4,0l-130.4,131h359.6L631,0h-359.6Z"></path>
					<path class="news0" d="M10,623.2v304.8l360-360.2v-304.8L10,623.2Z"></path>
					<path class="news2" d="M500.5,132l-130.5,130.5h304.5v304.5l130.5-130.5V132h-304.5Z"></path>
					</svg>
			</div>
			</div>

			<section class="choose-path">
				<div class="uk-container full-page section-content">
					<h2 class="script cyp">Choose Your Path!</h2>
					<h3 class="h3blu">Tysons Get Around Guide</h3>
					<p><b>Tysons Get Around Guide will help you navigate the best routes, scenic trails, unique destinations, convenient services, amazing food, and more.</b> Routes link Metrorail stations, grocery stores, retail, restaurants, parks, open spaces, trails and other community amenities. The routes highlight unique or useful destinations and safe, convenient connections between them. The routes help to stitch parts of Tysons together into a pretty special urban place.</p>
					<p><b>Download a PDF of the Tysons Get Around Guide or use the digital version of the routes on Google Maps to explore the destinations along the routes.</b></p>
					
					<div class="diptych">
						<div class="item ">
							<a href="https://tysonsva.org/wp-content/uploads/2025/06/Tysons_Get_Around_Guide_060525-DIGITAL-1.pdf" target="_blank">
							<img src="https://tysonsva.org/wp-content/uploads/2025/06/tysons_get_around_guide_map.png" alt="Get Around Guide Map">
							</a>
						</div>
						<div class="item ">
							<a href="https://www.google.com/maps/d/edit?mid=1WzbZl7wkY_7lH880K_4h9MALvkMWMVM&usp=sharing" target="_blank">
							<img src="/wp-content/uploads/2025/02/path_map-2.jpg" alt="Get Around Guide Map">
							</a>
						</div>
					</div>

					<h3 class="h3blu">Choose Your Path! Explore Tysons using the four routes: Pink, Green, Blue and Red.</h3>
					<p><strong>Learn more about the routes here:</strong></p>
					<a href="https://mailchi.mp/tysonsva/explore-tysons-12710207" target="_blank">Supervisor Palchik Walks the Walk</a>
					<br>
					<a href="https://mailchi.mp/tysonsva/explore-tysons-12712561" target="_blank">The Pink Route led to an Urban Oasis</a> 
					<br>
					<a href="https://mailchi.mp/tysonsva/explore-tysons-12713872" target="_blank">Scott's Run Trail: Another Hidden Gen in Tysons</a> 
					<br>
					<a href="https://mailchi.mp/tysonsva/explore-tysons-12714347" target="_blank">We walked, talked, and explored our community</a>
					<br>
					<a href="https://mailchi.mp/tysonsva/explore-tysons-12716937" target="_blank">Rolling with the Community Walk: A Fresh Perspective</a> 
				</div>
			</section>

			<section class="resources">
				<div class="uk-container full-page section-content">
					<img class="alignleft size-full" src="/wp-content/uploads/2025/02/getaround_gathering.jpg" alt="People gathered" width="400" height="auto">

					<h3 class="h3blu" style="margin-top:0.75em;">Over 200 miles walked to Explore Tysons!</h3>

					<p>The Tysons Get Around Guide was launched in June 2024 along with a summer walk challenge. Participants who walked any one of the designated routes received a $15 eGift Card, and those that completed all four routes received a $100 eGift Card. The walks were recorded on any fitness tracking app and submitted through an online form. The winners received a <a href="https://app.yiftee.com/gift-card/tysons-community-alliance--va" target="_blank">Tysons Community Card</a>, a new <strong>Yiftee eGift Card program</strong> spearheaded by the TCA to support local businesses.</p>  

					<p>The summer walk challenge was a huge success with <strong>41 people walking one of the four featured routes and 7 walking all four</strong>. Each route is about 3 miles in length which means participants walked more than 200 miles in and around Tysons! </p>

					<p>Read what participants had to say about the challenge at: <a href="https://mailchi.mp/tysonsva/explore-tysons-12716043" target="_blank">https://mailchi.mp/tysonsva/explore-tysons-12716043</a></p>
					<p>Whether you are new to Tysons or have been in the area for years, it is worth taking an active break to explore a route. So many of the trails, overpasses, and sidewalks make new connections to areas of Tysons that were previously difficult to access without a car. Better connections mean that the places you want to go may be closer than you think. With a short walk or ride, you may be in reach of your favorite grocery store, a great lunch spot, your workplace, a Metrorail station, a charming local park, your favorite stores, or even your child’s elementary school.</p>
				</div>
			</section>

			

			<div class="uk-container deco-wrap standard-container">
				<div class="section-decoration">
					<svg id="section-graphic-1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 554 646">
						<defs>
							<style>
								.shape-0 {
									fill: #260d66;
								}

								.shape-1 {
									fill: #00b852;
								}

								.shape-2 {
									fill: #9adcf7;
								}

								.shape-3 {
									fill: #385dff;
								}
							</style>
						</defs>
						<path class="shape-1" d="M0,181.9v121.3l121.2-121.3H0Z"></path>
						<path class="shape-2" d="M181.8,0l-90.9,90.9h250.7L432.5,0h-250.7Z"></path>
						<path class="shape-0" d="M0,433.6v212.2l250.7-250.8v-212.2L0,433.6Z"></path>
						<path class="shape-3" d="M341.6,91.9l-90.9,90.9h212.1v212.2l90.9-90.9V91.9h-212.1Z"></path>
					</svg>
				</div>
			</div>
		
	</main><!-- #main -->

<?php

get_footer();
