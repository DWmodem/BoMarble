<!DOCTYPE html>
<html>
<head>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.2/css/bootstrap-theme.min.css" type="text/css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.2/css/bootstrap.min.css" type="text/css" rel="stylesheet">

	<style type="text/css">
		.tile-row:first-child .tile {
			border-top: 1px solid #999;
		}

		.tile:first-child {
			border-left: 1px solid #999;
		}

		.tile {
			border-right: 1px solid #999;
			border-bottom: 1px solid #999;
			display: inline-block;
			float: left;
			width: 10px;
			height: 10px;

			/* Make sure the tiles can't be dragged, since it ruins our own click-and-drag effect. */
			-moz-user-select: none;
			-webkit-user-select: none;
		}

		.color-palette {
			display: inline-block;
			margin-bottom: 15px;
			padding: 10px;
			background: rgba(0, 0, 150, 0.2);
			border-radius: 5px;
			cursor: pointer;
		}

		.color-palette-tile {
			width: 20px;
			height: 20px;
			float: left;
			margin-left: 10px;
			border: 1px solid black;
			outline: 2px solid white
		}

		.color-palette-tile:first-of-type {
			margin-left: 0;
		}

		.color-palette-tile.selected {
			outline-color: rgb(255, 255, 100);
		}
	</style>
</head>

<body>
	<div class="container">
		<h1>Super Awesome Paint Program</h1>
		<div id="tiles"></div>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.2/js/bootstrap.min.js"></script>
	<script type="text/javascript">
	
		(function(){
			/**
			 * Constructor
			 *
			 * @param {HTMLElement|jQuery Object} container - A DOM element to use as a container for the tiles.
			 * @param {int} width - The number of tiles to create horizontally.
			 * @param {int} height - The number of tiles to create vertically.
			 */
			var Chess = function(container, width, height){

				this.container = $(container);

				this.tilesByXY = [];
				this.tilesByID = {};

				this.width = width;
				this.height = height;

				this.plugins = {};
			}

			/**
			 * Intializes the Chess Board
			 *
			 * First, this creates the tiles inside the specified container.
			 * Then it binds the events required to enable user interaction.
			 */
			Chess.prototype.init = function() {
				this._createTiles();
				this._bindEvents();
			};

			/**
			 * Creates the tiles inside the specified container.
			 */
			Chess.prototype._createTiles = function() {
				for (var y = 0; y < this.width; y++) {
					var row = $('<div class="tile-row clearfix">');

					for (var x = 0; x < this.height; x++) {
						var tile = new PaintTile(x, y);

						// Put the actual tile HTMLElement into the row.
						tile.getElement().appendTo(row);

						// Store tiles by x-y coordinates for easy retrieval when looking for neighbours.
						if (!(x in this.tilesByXY)) {
							this.tilesByXY[x] = [];
						}
						this.tilesByXY[x][y] = tile;

						// Store tiles by ID for easy retrieval by HTMLElement (in event handlers).
						this.tilesByID[tile.getID()] = tile;
					}

					row.appendTo(this.container);
				}
			};

			Chess.prototype._bindEvents = function() {
				var self = this;

				
				this.container.on('mouseenter', '.tile', function(e) {
					console.log("Mouseenter");
				});
			};
		})();

	// Start everything once the DOM is ready.
	$(document).ready(function() {
		var chess = new Chess($('#tiles'), 80, 80);
		chess.init();

		chess.registerPlugin('ColorPalette', new ColorPalette());
		chess.registerPlugin('FloodFill', new FloodFill());

		// For debugging purposes, make the instance available to the console.
		window.chess = chess;
	});
	</script>
</body>
</html>

