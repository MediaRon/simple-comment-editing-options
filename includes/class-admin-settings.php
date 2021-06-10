<?php
/**
 * Output SCE Options.
 *
 * @package SCEOptions
 */

namespace SCEOptions\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
}

use SCEOptions\Includes\Options as Options;
use SCEOptions\Includes\Functions as Functions;

/**
 * Class Functions
 */
class Admin_Settings {
	/**
	 * Registers and outputs placeholder for settings.
	 *
	 * @since 1.0.0
	 */
	public static function settings_page() {
		?>
		<div class="wrap sce-settings-header">
			<?php
			self::get_settings_header();
			?>
		</div>
		<div class="wrap sce-settings-tabs">
			<?php
			self::get_settings_tabs();
			?>
		</div>
		<?php
		self::get_settings_footer();
		?>
		<?php
	}

	/**
	 * Output Generic Settings Place holder for React goodness.
	 */
	public static function get_settings_header() {
		echo 'blah';
	}

	/**
	 * Get the admin footer.
	 */
	public static function get_admin_header() {
		// Make sure we enqueue on the right admin screen.
		$screen = get_current_screen();
		if ( ! isset( $screen->id ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( isset( $screen->base ) && 'settings_page_sce' !== $screen->base ) {
			return;
		}
		?>
		<svg width="0" height="0" class="hidden">
			<symbol aria-hidden="true" data-prefix="fas" data-icon="life-ring" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="life-ring-solid">
				<path fill="currentColor" d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm173.696 119.559l-63.399 63.399c-10.987-18.559-26.67-34.252-45.255-45.255l63.399-63.399a218.396 218.396 0 0145.255 45.255zM256 352c-53.019 0-96-42.981-96-96s42.981-96 96-96 96 42.981 96 96-42.981 96-96 96zM127.559 82.304l63.399 63.399c-18.559 10.987-34.252 26.67-45.255 45.255l-63.399-63.399a218.372 218.372 0 0145.255-45.255zM82.304 384.441l63.399-63.399c10.987 18.559 26.67 34.252 45.255 45.255l-63.399 63.399a218.396 218.396 0 01-45.255-45.255zm302.137 45.255l-63.399-63.399c18.559-10.987 34.252-26.67 45.255-45.255l63.399 63.399a218.403 218.403 0 01-45.255 45.255z"></path>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="print" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="print-solid">
				<path fill="currentColor" d="M448 192V77.25c0-8.49-3.37-16.62-9.37-22.63L393.37 9.37c-6-6-14.14-9.37-22.63-9.37H96C78.33 0 64 14.33 64 32v160c-35.35 0-64 28.65-64 64v112c0 8.84 7.16 16 16 16h48v96c0 17.67 14.33 32 32 32h320c17.67 0 32-14.33 32-32v-96h48c8.84 0 16-7.16 16-16V256c0-35.35-28.65-64-64-64zm-64 256H128v-96h256v96zm0-224H128V64h192v48c0 8.84 7.16 16 16 16h48v96zm48 72c-13.25 0-24-10.75-24-24 0-13.26 10.75-24 24-24s24 10.74 24 24c0 13.25-10.75 24-24 24z"></path>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="print" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="shield-check">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M466.5 83.67l-192-80a48.15 48.15 0 0 0-36.9 0l-192 80A48 48 0 0 0 16 128c0 198.5 114.5 335.69 221.5 380.29a48.15 48.15 0 0 0 36.9 0C360.1 472.58 496 349.27 496 128a48 48 0 0 0-29.5-44.33zm-47.2 114.21l-184 184a16.06 16.06 0 0 1-22.6 0l-104-104a16.07 16.07 0 0 1 0-22.61l22.6-22.6a16.07 16.07 0 0 1 22.6 0l70.1 70.1 150.1-150.1a16.07 16.07 0 0 1 22.6 0l22.6 22.6a15.89 15.89 0 0 1 0 22.61z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M419.3 197.88l-184 184a16.06 16.06 0 0 1-22.6 0l-104-104a16.07 16.07 0 0 1 0-22.61l22.6-22.6a16.07 16.07 0 0 1 22.6 0l70.1 70.1 150.1-150.1a16.07 16.07 0 0 1 22.6 0l22.6 22.6a15.89 15.89 0 0 1 0 22.61z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="print" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="table">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M288 160v96h160v-96zm0 256h160v-96H288zM64 256h160v-96H64zm0 160h160v-96H64z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M464 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h416a48 48 0 0 0 48-48V80a48 48 0 0 0-48-48zM224 416H64v-96h160zm0-160H64v-96h160zm224 160H288v-96h160zm0-160H288v-96h160z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="eraser" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="eraser">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M512 428v40a12 12 0 0 1-12 12H144a48 48 0 0 1-33.94-14.06l-96-96a48 48 0 0 1 0-67.88l136-136 227.88 227.88L355.88 416H500a12 12 0 0 1 12 12z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M377.94 393.94l120-120a48 48 0 0 0 0-67.88l-160-160a48 48 0 0 0-67.88 0l-120 120 45.25 45.25z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="sun-cloud" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" id="sun-cloud">
				<g class="fa-group"><path d="M325 363C305 376 282 384 256 384C185 384 128 327 128 256C128 185 185 128 256 128C302 128 342 153 365 189C380 159 408 138 441 131L390 122L371 13C369 2 356 -4 347 3L256 66L165 3C156 -4 143 2 141 13L122 122L13 141C2 143 -4 156 3 165L66 256L3 347C-4 356 2 369 13 371L122 390L141 499C143 510 156 516 165 509L256 446L347 509C356 516 369 510 371 499L392 384H384C362 384 342 376 325 363ZM337 205C320 178 290 160 256 160C203 160 160 203 160 256S203 352 256 352C273 352 289 347 303 339C294 324 288 307 288 288C288 252 308 221 337 205Z" class="fa-secondary" fill="currentColor" opacity="0.4" /><path d="M576 224C564 224 553 227 543 233C540 192 506 160 464 160C425 160 393 188 386 224C385 224 385 224 384 224C349 224 320 253 320 288S349 352 384 352H576C611 352 640 323 640 288S611 224 576 224Z" class="fa-primary" fill="currentColor"/></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="dashboard" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" id="dashboard">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M288 32C128.94 32 0 160.94 0 320a286.5 286.5 0 0 0 39.06 144.8c5.61 9.62 16.3 15.2 27.44 15.2h443c11.14 0 21.83-5.58 27.44-15.2A286.5 286.5 0 0 0 576 320c0-159.06-128.94-288-288-288zm55.12 384H232.88a62.26 62.26 0 0 1 .47-64.86L124.8 206.41a24 24 0 0 1 38.41-28.81l108.56 144.74A63.5 63.5 0 0 1 343.12 416z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M343.12 416H232.88a62.26 62.26 0 0 1 .47-64.86L124.8 206.41a24 24 0 0 1 38.41-28.81l108.56 144.74A63.5 63.5 0 0 1 343.12 416z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="database" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" id="database">
				<g class="fa-group"><path d="M448 73V119C448 159 348 192 224 192S0 159 0 119V73C0 33 100 0 224 0S448 33 448 73Z" class="fa-secondary" fill="currentColor" opacity="0.4" /><path d="M224 225C136 225 48 209 0 176V279C0 319 100 352 224 352S448 319 448 279V176C400 209 312 225 224 225ZM0 336V439C0 479 100 512 224 512S448 479 448 439V336C400 369 312 385 224 385S48 369 0 336Z" class="fa-primary" fill="currentColor"/></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="screwdriver-wrench" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="screwdriver-wrench">
				<g class="fa-group"><path d="M228 307L169 248L20 397C-7 423 -7 466 20 492C33 505 50 512 67 512S102 505 115 492L234 373C225 352 223 329 228 307ZM64 472C51 472 40 461 40 448C40 435 51 424 64 424S88 435 88 448C88 461 77 472 64 472ZM508 109C506 103 500 99 493 101C491 101 489 102 488 104L413 178L345 167L334 99L408 24C413 20 413 12 408 7C407 6 405 5 403 4C354 -8 302 6 266 42C226 82 215 139 232 190L228 194L299 265C327 251 361 256 384 279L391 286C421 281 449 267 470 246C506 210 520 158 508 109Z" class="fa-secondary" fill="currentColor" opacity="0.4" /><path d="M384 279C361 255 326 251 299 265L192 158V96L64 0L0 64L96 192H158L265 299C251 326 255 361 279 384L396 501C410 516 434 516 448 501L501 448C516 434 516 410 501 396L384 279Z" class="fa-primary" fill="currentColor"/></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="cloud-refresh" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="cloud-refresh">
				<g class="fa-group"><path d="M228 307L169 248L20 397C-7 423 -7 466 20 492C33 505 50 512 67 512S102 505 115 492L234 373C225 352 223 329 228 307ZM64 472C51 472 40 461 40 448C40 435 51 424 64 424S88 435 88 448C88 461 77 472 64 472ZM508 109C506 103 500 99 493 101C491 101 489 102 488 104L413 178L345 167L334 99L408 24C413 20 413 12 408 7C407 6 405 5 403 4C354 -8 302 6 266 42C226 82 215 139 232 190L228 194L299 265C327 251 361 256 384 279L391 286C421 281 449 267 470 246C506 210 520 158 508 109Z" class="fa-secondary" fill="currentColor" opacity="0.4" /><path d="M384 279C361 255 326 251 299 265L192 158V96L64 0L0 64L96 192H158L265 299C251 326 255 361 279 384L396 501C410 516 434 516 448 501L501 448C516 434 516 410 501 396L384 279Z" class="fa-primary" fill="currentColor"/></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="at" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="at">
				<g class="fa-group"><path d="M496 256V277C496 327 462 372 414 382C404 384 394 385 384 384V319C387 319 389 320 392 320C414 320 432 302 432 280V264C432 172 365 90 274 81C169 71 80 153 80 256C80 348 151 424 241 432C249 433 256 439 256 448V480C256 489 248 497 239 496C99 486 -8 356 21 208C39 114 114 39 208 21C361 -9 496 108 496 256Z" class="fa-secondary" fill="currentColor" opacity="0.4" /><path d="M384 319C366 315 352 300 352 280V160C352 151 345 144 336 144H304C297 144 291 149 289 156C274 149 258 144 240 144C178 144 128 194 128 256S178 368 240 368C266 368 290 359 310 344C327 366 354 382 384 384V319ZM240 304C214 304 192 283 192 256S214 208 240 208S288 230 288 256S266 304 240 304Z" class="fa-primary" fill="currentColor"/></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="table" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="table">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M288 160v96h160v-96zm0 256h160v-96H288zM64 256h160v-96H64zm0 160h160v-96H64z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M464 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h416a48 48 0 0 0 48-48V80a48 48 0 0 0-48-48zM224 416H64v-96h160zm0-160H64v-96h160zm224 160H288v-96h160zm0-160H288v-96h160z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="tachometer-fastest" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" id="tachometer-fastest">
			<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M288 32C128.94 32 0 160.94 0 320a286.5 286.5 0 0 0 39.06 144.8c5.61 9.62 16.3 15.2 27.44 15.2h443c11.14 0 21.83-5.58 27.44-15.2A286.5 286.5 0 0 0 576 320c0-159.06-128.94-288-288-288zm196 343.67L350 398a66 66 0 0 1-6.9 18H232.88a63.33 63.33 0 0 1-8.88-32 63.85 63.85 0 0 1 118.37-33.39l133.68-22.28a24 24 0 0 1 7.9 47.34z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M484 375.67L350 398a66 66 0 0 1-6.9 18H232.88a63.33 63.33 0 0 1-8.88-32 63.85 63.85 0 0 1 118.37-33.39l133.68-22.28a24 24 0 0 1 7.9 47.34z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="home-heart" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" id="home-heart">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M64.11 311.38V496a16.05 16.05 0 0 0 16 16h416a16.05 16.05 0 0 0 16-16V311.38c-6.7-5.5-44.7-38.31-224-196.4-180.11 158.9-217.6 191.09-224 196.4zm314.1-26.31a60.6 60.6 0 0 1 4.5 89.11L298 459.77a13.94 13.94 0 0 1-19.8 0l-84.7-85.59a60.66 60.66 0 0 1 4.3-89.11c24-20 59.7-16.39 81.6 5.81l8.6 8.69 8.6-8.69c22.01-22.2 57.71-25.81 81.61-5.81z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M378.21 285.07c-23.9-20-59.6-16.39-81.6 5.81l-8.6 8.69-8.6-8.69c-21.9-22.2-57.6-25.81-81.6-5.81a60.66 60.66 0 0 0-4.3 89.11l84.7 85.59a13.94 13.94 0 0 0 19.8 0l84.7-85.59a60.6 60.6 0 0 0-4.5-89.11zm192.6-48.8l-58.7-51.79V48a16 16 0 0 0-16-16h-64a16 16 0 0 0-16 16v51.7l-101.3-89.43a40 40 0 0 0-53.5 0l-256 226a16 16 0 0 0-1.2 22.61l21.4 23.8a16 16 0 0 0 22.6 1.2l229.4-202.2a16.12 16.12 0 0 1 21.2 0L528 284a16 16 0 0 0 22.6-1.21L572 259a16.11 16.11 0 0 0-1.19-22.73z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="plug" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" id="plug">
				<path fill="currentColor" d="M320,32a32,32,0,0,0-64,0v96h64Zm48,128H16A16,16,0,0,0,0,176v32a16,16,0,0,0,16,16H32v32A160.07,160.07,0,0,0,160,412.8V512h64V412.8A160.07,160.07,0,0,0,352,256V224h16a16,16,0,0,0,16-16V176A16,16,0,0,0,368,160ZM128,32a32,32,0,0,0-64,0v96h64Z"></path>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="image" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="image">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M448 384H64v-48l71.51-71.52a12 12 0 0 1 17 0L208 320l135.51-135.52a12 12 0 0 1 17 0L448 272z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M464 64H48a48 48 0 0 0-48 48v288a48 48 0 0 0 48 48h416a48 48 0 0 0 48-48V112a48 48 0 0 0-48-48zm-352 56a56 56 0 1 1-56 56 56 56 0 0 1 56-56zm336 264H64v-48l71.51-71.52a12 12 0 0 1 17 0L208 320l135.51-135.52a12 12 0 0 1 17 0L448 272z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="checkcircle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="checkcircle">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm155.31 195.31l-184 184a16 16 0 0 1-22.62 0l-104-104a16 16 0 0 1 0-22.62l22.62-22.63a16 16 0 0 1 22.63 0L216 308.12l150.06-150.06a16 16 0 0 1 22.63 0l22.62 22.63a16 16 0 0 1 0 22.62z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M227.31 387.31a16 16 0 0 1-22.62 0l-104-104a16 16 0 0 1 0-22.62l22.62-22.63a16 16 0 0 1 22.63 0L216 308.12l150.06-150.06a16 16 0 0 1 22.63 0l22.62 22.63a16 16 0 0 1 0 22.62l-184 184z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="ban" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="ban">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M406.78 361.53a186.53 186.53 0 0 1-45.25 45.25L105.22 150.47a186.53 186.53 0 0 1 45.25-45.25z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm130.11 378.11A184 184 0 1 1 440 256a182.82 182.82 0 0 1-53.89 130.11z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="spinner" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="spinner">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M108.92 355.08a48 48 0 1 0 48 48 48 48 0 0 0-48-48zM256 416a48 48 0 1 0 48 48 48 48 0 0 0-48-48zm208-208a48 48 0 1 0 48 48 48 48 0 0 0-48-48zm-60.92 147.08a48 48 0 1 0 48 48 48 48 0 0 0-48-48zm0-198.16a48 48 0 1 0-48-48 48 48 0 0 0 48 48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M108.92 60.92a48 48 0 1 0 48 48 48 48 0 0 0-48-48zM48 208a48 48 0 1 0 48 48 48 48 0 0 0-48-48zM256 0a48 48 0 1 0 48 48 48 48 0 0 0-48-48z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="save" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" id="save">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M288 352a64 64 0 1 1-64-64 64 64 0 0 1 64 64z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M433.94 129.94l-83.88-83.88A48 48 0 0 0 316.12 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V163.88a48 48 0 0 0-14.06-33.94zM224 416a64 64 0 1 1 64-64 64 64 0 0 1-64 64zm96-204a12 12 0 0 1-12 12H76a12 12 0 0 1-12-12V108a12 12 0 0 1 12-12h228.52a12 12 0 0 1 8.48 3.52l3.48 3.48a12 12 0 0 1 3.52 8.48z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="sync" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="sync">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M8 454.06V320a24 24 0 0 1 24-24h134.06c21.38 0 32.09 25.85 17 41l-41.75 41.75A166.82 166.82 0 0 0 256.16 424c77.41-.07 144.31-53.14 162.78-126.85a12 12 0 0 1 11.65-9.15h57.31a12 12 0 0 1 11.81 14.18C478.07 417.08 377.19 504 256 504a247.14 247.14 0 0 1-171.31-68.69L49 471c-15.15 15.15-41 4.44-41-16.94z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M12.3 209.82C33.93 94.92 134.81 8 256 8a247.14 247.14 0 0 1 171.31 68.69L463 41c15.12-15.12 41-4.41 41 17v134a24 24 0 0 1-24 24H345.94c-21.38 0-32.09-25.85-17-41l41.75-41.75A166.8 166.8 0 0 0 255.85 88c-77.46.07-144.33 53.18-162.79 126.85A12 12 0 0 1 81.41 224H24.1a12 12 0 0 1-11.8-14.18z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="plug" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" id="plug">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M288,0a32,32,0,0,0-32,32V160h64V32A32,32,0,0,0,288,0ZM96,0A32,32,0,0,0,64,32V160h64V32A32,32,0,0,0,96,0Z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M384,176v32a16,16,0,0,1-16,16H352v32A160.07,160.07,0,0,1,224,412.8V512H160V412.8A160.07,160.07,0,0,1,32,256V224H16A16,16,0,0,1,0,208V176a16,16,0,0,1,16-16H368A16,16,0,0,1,384,176Z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="paintbrush" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="paintbrush">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M512 49.55c0 16.14-6.52 31.64-13.9 46C385.06 306.53 349.06 352 287 352a92 92 0 0 1-22.39-3l-63.82-53.18a92.58 92.58 0 0 1-8.73-38.7c0-53.75 21.27-58 225.68-240.64C428.53 6.71 442.74 0 457.9 0 486 0 512 20.64 512 49.55z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M255 382.68a86.64 86.64 0 0 1 1 9.13C256 468.23 203.87 512 128 512 37.94 512 0 439.62 0 357.27c9.79 6.68 44.14 34.35 55.25 34.35a15.26 15.26 0 0 0 14.59-10c20.66-54.44 57.07-69.72 97.19-72.3z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="language" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" id="language">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M168.1 236.2c-3.5-12.1-7.8-33.2-7.8-33.2h-.5s-4.3 21.1-7.8 33.2l-11.1 37.5H179zM0 120v272.12A23.94 23.94 0 0 0 24 416h296V96H23.88A23.94 23.94 0 0 0 0 120zm74.62 216.19l57.65-168.14A12 12 0 0 1 143.7 160h32.58a12.23 12.23 0 0 1 11.43 8.05l57.64 168.14a11.7 11.7 0 0 1 .65 3.93A12 12 0 0 1 233.92 352H211a12 12 0 0 1-11.53-8.55L190 311.73h-60.34l-9.12 31.62A12.11 12.11 0 0 1 109 352H86a12.07 12.07 0 0 1-11.43-15.81zM564 188h-64v-16a12 12 0 0 0-12-12h-16a12 12 0 0 0-12 12v16h-64a12 12 0 0 0-12 12v16a12 12 0 0 0 12 12h114.3c-6.2 14.3-16.5 29-30 43.19a191 191 0 0 1-17.4-20.89 12.09 12.09 0 0 0-16-3.4l-7.3 4.3-6.5 3.89-.64.41a12 12 0 0 0-3.06 16.69 231.81 231.81 0 0 0 21 25.69 284.34 284.34 0 0 1-26.1 18 12 12 0 0 0-4.2 16.2l7.9 13.89.2.34a12 12 0 0 0 16.5 4 352.44 352.44 0 0 0 35.4-24.89 348.11 348.11 0 0 0 35.4 24.89 3.79 3.79 0 0 0 .34.2 12 12 0 0 0 16.36-4.5l7.9-14.01a12 12 0 0 0-4.1-16.2 310.64 310.64 0 0 1-26.1-18c21-22.49 35.8-46.28 42.7-69.88H564a12 12 0 0 0 12-12V200a12 12 0 0 0-12-12z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M616.12 96H320v320h296a23.94 23.94 0 0 0 24-23.88V120a23.94 23.94 0 0 0-23.88-24zM576 216a12 12 0 0 1-12 12h-11.4c-6.9 23.6-21.7 47.39-42.7 69.88a310.64 310.64 0 0 0 26.1 18 12 12 0 0 1 4.1 16.2l-7.9 13.89a12 12 0 0 1-16.36 4.5 3.79 3.79 0 0 1-.34-.2 348.11 348.11 0 0 1-35.4-24.89 352.44 352.44 0 0 1-35.4 24.89 12 12 0 0 1-16.5-4l-.2-.34-7.9-13.93a12 12 0 0 1 4.2-16.2 284.34 284.34 0 0 0 26.1-18 231.81 231.81 0 0 1-21-25.69 12 12 0 0 1 3.06-16.69l.64-.41 6.5-3.89 7.3-4.3a12.09 12.09 0 0 1 16 3.4 191 191 0 0 0 17.4 20.89c13.5-14.2 23.8-28.89 30-43.19H396a12 12 0 0 1-12-12V200a12 12 0 0 1 12-12h64v-16a12 12 0 0 1 12-12h16a12 12 0 0 1 12 12v16h64a12 12 0 0 1 12 12zm-388.29-47.95a12.23 12.23 0 0 0-11.43-8.05H143.7a12 12 0 0 0-11.43 8.05L74.62 336.19A12.07 12.07 0 0 0 86.05 352h23a12.11 12.11 0 0 0 11.53-8.65l9.12-31.62H190l9.42 31.72A12 12 0 0 0 211 352h23a12 12 0 0 0 12-11.88 11.7 11.7 0 0 0-.65-3.93zM140.9 273.7l11.1-37.5c3.5-12.1 7.8-33.2 7.8-33.2h.5s4.3 21.1 7.8 33.2l10.9 37.5z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="cloud-upload" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" id="cloud-upload">
				<path fill="currentColor" d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zm-139.9 63.7l-10.8 10.8c-9.6 9.6-25.2 9.3-34.5-.5L320 266.1V392c0 13.3-10.7 24-24 24h-16c-13.3 0-24-10.7-24-24V266.1l-32.4 34.5c-9.3 9.9-24.9 10.1-34.5.5l-10.8-10.8c-9.4-9.4-9.4-24.6 0-33.9l92.7-92.7c9.4-9.4 24.6-9.4 33.9 0l92.7 92.7c9.4 9.4 9.4 24.6.1 33.9z"></path>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fas" data-icon="warning" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" id="warning">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M569.52 440L329.58 24c-18.44-32-64.69-32-83.16 0L6.48 440c-18.42 31.94 4.64 72 41.57 72h479.89c36.87 0 60.06-40 41.58-72zM288 448a32 32 0 1 1 32-32 32 32 0 0 1-32 32zm38.24-238.41l-12.8 128A16 16 0 0 1 297.52 352h-19a16 16 0 0 1-15.92-14.41l-12.8-128A16 16 0 0 1 265.68 192h44.64a16 16 0 0 1 15.92 17.59z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M310.32 192h-44.64a16 16 0 0 0-15.92 17.59l12.8 128A16 16 0 0 0 278.48 352h19a16 16 0 0 0 15.92-14.41l12.8-128A16 16 0 0 0 310.32 192zM288 384a32 32 0 1 0 32 32 32 32 0 0 0-32-32z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fab" data-icon="wordpress" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="wordpress">
				<path fill="currentColor" d="M61.7 169.4l101.5 278C92.2 413 43.3 340.2 43.3 256c0-30.9 6.6-60.1 18.4-86.6zm337.9 75.9c0-26.3-9.4-44.5-17.5-58.7-10.8-17.5-20.9-32.4-20.9-49.9 0-19.6 14.8-37.8 35.7-37.8.9 0 1.8.1 2.8.2-37.9-34.7-88.3-55.9-143.7-55.9-74.3 0-139.7 38.1-177.8 95.9 5 .2 9.7.3 13.7.3 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l77.5 230.4L249.8 247l-33.1-90.8c-11.5-.7-22.3-2-22.3-2-11.5-.7-10.1-18.2 1.3-17.5 0 0 35.1 2.7 56 2.7 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l76.9 228.7 21.2-70.9c9-29.4 16-50.5 16-68.7zm-139.9 29.3l-63.8 185.5c19.1 5.6 39.2 8.7 60.1 8.7 24.8 0 48.5-4.3 70.6-12.1-.6-.9-1.1-1.9-1.5-2.9l-65.4-179.2zm183-120.7c.9 6.8 1.4 14 1.4 21.9 0 21.6-4 45.8-16.2 76.2l-65 187.9C426.2 403 468.7 334.5 468.7 256c0-37-9.4-71.8-26-102.1zM504 256c0 136.8-111.3 248-248 248C119.2 504 8 392.7 8 256 8 119.2 119.2 8 256 8c136.7 0 248 111.2 248 248zm-11.4 0c0-130.5-106.2-236.6-236.6-236.6C125.5 19.4 19.4 125.5 19.4 256S125.6 492.6 256 492.6c130.5 0 236.6-106.1 236.6-236.6z"></path>
			</symbol>
			<div id="sce-fancybox-loading-spinner-saving" aria-hidden="true" style="display: none;">
				<h2><?php esc_attr_e( 'Saving...', 'ultimate-auto-updates' ); ?></h2>
				<svg class="sce-icon sce-icon-spinner slow" arial-label="<?php esc_attr_e( 'Saving...', 'ultimate-auto-updates' ); ?>">
					<use xlink:href="#spinner"></use>
				</svg>
			</div>
			<div id="sce-fancybox-loading-spinner-deleting" aria-hidden="true" style="display: none;">
				<h2><?php esc_attr_e( 'Removing...', 'ultimate-auto-updates' ); ?></h2>
				<svg class="sce-icon sce-icon-spinner slow" arial-label="<?php esc_attr_e( 'Removing...', 'ultimate-auto-updates' ); ?>">
					<use xlink:href="#spinner"></use>
				</svg>
			</div>
			<div id="sce-fancybox-loading-spinner-resetting" aria-hidden="true" style="display: none;">
				<h2><?php esc_attr_e( 'Resetting...', 'ultimate-auto-updates' ); ?></h2>
				<svg class="sce-icon sce-icon-spinner slow" arial-label="<?php esc_attr_e( 'Resetting...', 'ultimate-auto-updates' ); ?>">
					<use xlink:href="#spinner"></use>
				</svg>
			</div>
			<div id="sce-fancybox-loading-spinner-clearing" aria-hidden="true" style="display: none;">
				<h2><?php esc_attr_e( 'Clearing...', 'ultimate-auto-updates' ); ?></h2>
				<svg class="sce-icon sce-icon-spinner slow" arial-label="<?php esc_attr_e( 'Clearing...', 'ultimate-auto-updates' ); ?>">
					<use xlink:href="#spinner"></use>
				</svg>
			</div>
			<?php do_action( 'sceo_add_admin_header_content' ); ?>
		<?php
	}
	/**
	 * Output the top-level admin tabs.
	 */
	public static function get_settings_tabs() {
		$settings_url_base = Functions::get_settings_url( 'main' )
		?>
			<?php
			$tabs = array();
			/**
			 * Filer the output of the tab output.
			 *
			 * Potentially modify or add your own tabs.
			 *
			 * @since 1.0.0
			 *
			 * @param array $tabs Associative array of tabs.
			 */
			$tabs       = apply_filters( 'sceo_admin_tabs', $tabs );
			$tab_html   = '<nav class="nav-tab-wrapper">';
			$tabs_count = count( $tabs );
			if ( $tabs && ! empty( $tabs ) && is_array( $tabs ) ) {
				$active_tab = Functions::get_admin_tab();
				if ( null === $active_tab ) {
					$active_tab = 'main';
				}
				$is_tab_match = false;
				if ( 'main' === $active_tab ) {
					$active_tab = 'main';
				} else {
					foreach ( $tabs as $tab ) {
						$tab_get = isset( $tab['get'] ) ? $tab['get'] : '';
						if ( $active_tab === $tab_get ) {
							$is_tab_match = true;
						}
					}
					if ( ! $is_tab_match ) {
						$active_tab = 'main';
					}
				}
				$do_action = false;
				foreach ( $tabs as $tab ) {
					$classes = array( 'nav-tab' );
					$tab_get = isset( $tab['get'] ) ? $tab['get'] : '';
					if ( $active_tab === $tab_get ) {
						$classes[] = 'nav-tab-active';
						$do_action = isset( $tab['action'] ) ? $tab['action'] : false;
					} elseif ( ! $is_tab_match && 'main' === $tab_get ) {
						$classes[] = 'nav-tab-active';
						$do_action = isset( $tab['action'] ) ? $tab['action'] : false;
					}
					$tab_url   = isset( $tab['url'] ) ? $tab['url'] : '';
					$tab_label = isset( $tab['label'] ) ? $tab['label'] : '';
					$tab_html .= sprintf(
						'<a href="%s" class="%s" id="eff-%s"><svg class="sce-icon sce-icon-tab">%s</svg><span>%s</span></a>',
						esc_url( $tab_url ),
						esc_attr( implode( ' ', $classes ) ),
						esc_attr( $tab_get ),
						sprintf( '<use xlink:href="#%s"></use>', esc_attr( $tab['icon'] ) ),
						esc_html( $tab['label'] )
					);
				}
				$tab_html .= '</nav>';
				if ( $tabs_count > 0 ) {
					echo wp_kses( $tab_html, Functions::get_kses_allowed_html() );
				}

				$current_tab     = Functions::get_admin_tab();
				$current_sub_tab = Functions::get_admin_sub_tab();

				/**
				 * Filer the output of the sub-tab output.
				 *
				 * Potentially modify or add your own sub-tabs.
				 *
				 * @since 1.0.0
				 *
				 * @param array Associative array of tabs.
				 * @param string Tab
				 * @param string Sub Tab
				 */
				$sub_tabs = apply_filters( 'sceo_admin_sub_tabs', array(), $current_tab, $current_sub_tab );

				// Check to see if no tabs are available for this view.
				if ( null === $current_tab && null === $current_sub_tab ) {
					$current_tab = 'main';
				}
				if ( $sub_tabs && ! empty( $sub_tabs ) && is_array( $sub_tabs ) ) {
					if ( null === $current_sub_tab ) {
						$current_sub_tab = '';
					}
					$is_tab_match      = false;
					$first_sub_tab     = current( $sub_tabs );
					$first_sub_tab_get = $first_sub_tab['get'];
					if ( $first_sub_tab_get === $current_sub_tab ) {
						$active_tab = $current_sub_tab;
					} else {
						$active_tab = $current_sub_tab;
						foreach ( $sub_tabs as $tab ) {
							$tab_get = isset( $tab['get'] ) ? $tab['get'] : '';
							if ( $active_tab === $tab_get ) {
								$is_tab_match = true;
							}
						}
						if ( ! $is_tab_match ) {
							$active_tab = $first_sub_tab_get;
						}
					}
					$sub_tab_html_array = array();
					$do_subtab_action   = false;
					$maybe_sub_tab      = '';
					foreach ( $sub_tabs as $sub_tab ) {
						$classes = array( 'sce-sub-tab' );
						$tab_get = isset( $sub_tab['get'] ) ? $sub_tab['get'] : '';
						if ( $active_tab === $tab_get ) {
							$classes[]        = 'sce-sub-tab-active';
							$do_subtab_action = true;
							$current_sub_tab  = $tab_get;
						} elseif ( ! $is_tab_match && $first_sub_tab_get === $tab_get ) {
							$classes[]        = 'sce-sub-tab-active';
							$do_subtab_action = true;
							$current_sub_tab  = $first_sub_tab_get;
						}
						$tab_url   = isset( $sub_tab['url'] ) ? $sub_tab['url'] : '';
						$tab_label = isset( $sub_tab['label'] ) ? $sub_tab['label'] : '';
						if ( $current_sub_tab === $tab_get ) {
							$sub_tab_html_array[] = sprintf( '<span class="%s" id="sce-tab-%s">%s</span>', esc_attr( implode( ' ', $classes ) ), esc_attr( $tab_get ), esc_html( $sub_tab['label'] ) );
						} else {
							$sub_tab_html_array[] = sprintf( '<a href="%s" class="%s" id="sce-tab-%s">%s</a>', esc_url( $tab_url ), esc_attr( implode( ' ', $classes ) ), esc_attr( $tab_get ), esc_html( $sub_tab['label'] ) );
						}
					}
					if ( ! empty( $sub_tab_html_array ) ) {
						echo '<nav class="sce-sub-links">' . wp_kses_post( rtrim( implode( ' | ', $sub_tab_html_array ), ' | ' ) ) . '</nav>';
					}
					if ( $do_subtab_action ) {
						/**
						 * Perform a sub tab action.
						 *
						 * Perform a sub tab action. Useful for loading scripts or inline styles as necessary.
						 *
						 * @since 1.0.0
						 *
						 * eff_admin_sub_tab_{current_tab}_{current_sub_tab}
						 * @param string Sub Tab
						 */
						do_action(
							sprintf( // phpcs:ignore
								'sceo_admin_sub_tab_%s_%s',
								sanitize_title( $current_tab ),
								sanitize_title( $current_sub_tab )
							)
						);
					}
				}
				if ( $do_action ) {

					/**
					 * Perform a tab action.
					 *
					 * Perform a tab action.
					 *
					 * @since 1.0.0
					 *
					 * @param string $action Can be any action.
					 * @param string Tab
					 * @param string Sub Tab
					 */
					do_action( $do_action, $current_tab, $current_sub_tab );
				}
			}
			?>
		<?php
	}

	/**
	 * Run script and enqueue stylesheets and stuff like that.
	 */
	public static function get_settings_footer() {
		// Do settings footer stuff here.
	}
}
