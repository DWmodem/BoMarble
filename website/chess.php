<!DOCTYPE html>
<html>
<head>
	<link href="css/bootstrap-theme.min.css" type="text/css" rel="stylesheet">
	<link href="css/bootstrap.min.css" type="text/css" rel="stylesheet">

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
			width: 90px;
			height: 90px;
			font-size: 80px;

			/* Make sure the tiles can't be dragged, since it ruins our own click-and-drag effect. */
			-moz-user-select: none;
			-webkit-user-select: none;
		}

		.center {
			width: 65%;
		    margin: 0 auto;
		}

		.piece {
			width: 100%;
			height: 100%;
			font-size: 100%;
		}
	</style>
</head>

<body>
	<div class="container">
		<h1>So you think you can chess?</h1>
		<div id="tiles" class="center"></div>
	</div>

	<script><?php include("../javascript/jquery-2.1.4.min.js");?></script>
	<script><?php include("js/bootstrap.min.js");?></script>
	<script>
	
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
			Chess.prototype._createTiles = function(alternateColors) {
				
				if(alternateColors == null){
					alternateColors = ["white", "lightgrey"];
				}

				for (var y = 0; y < this.width; y++) {
					var row = $('<div class="tile-row clearfix">');

					for (var x = 0; x < this.height; x++) {
						var color = alternateColors[(y+x) % alternateColors.length]; //Beautiful Solution
						var tile = new ChessTile(x, y, color);

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
			window.Chess = Chess;
		})();

		(function() {

		/**
		 * Constructor.
		 * The constructor creates an HTMLElement and generates a unique ID for it.
		 *
		 * @param {number} x - The x-coordinate of the tile.
		 * @param {number} y - The y-coordinate of the tile.
		 */
		var ChessTile = function(x, y, color) {
			this.piece = new ChessPiece("pawn", player1);
			this.$element = $('<div class="tile">');
			this.$element.prop('id', 'tile-' + x + '-' + y);
			this.$element.append(this.piece.getElement());
			this.x = x;
			this.y = y;

			this.setColor(color);
		}

		/**
		 * @returns {HTMLElement} The HTMLElement that this tile wraps.
		 */
		ChessTile.prototype.getElement = function() {
			return this.$element;
		}

		/**
		 * @returns {string} The ID of the tile (this is also used as the HTML ID attribute).
		 */
		ChessTile.prototype.getID = function() {
			return this.$element.prop('id');
		}

		/**
		 * @returns {number} The x-coordinate of the tile.
		 */
		ChessTile.prototype.getX = function() {
			return this.x;
		};

		/**
		 * @returns {number} The y-coordinate of the tile.
		 */
		ChessTile.prototype.getY = function() {
			return this.y;
		};

		/**
		 * @returns {Color} The color of the tile.
		 */
		ChessTile.prototype.getColor = function() {
			return this.color;
		};

		/**
		 * Sets the color of the tile.
		 *
		 * @param {Color} color - The new color of the tile.
		 */
		ChessTile.prototype.setColor = function(color) {
			this.color = color;
			this.$element[0].style.backgroundColor = color;
		};

		window.ChessTile = ChessTile;
	})();

		(function() {

		var ChessPiece = function(type, owner) {
			this.pieceValue = "&#9817";
			this.$element = '<div class="piece">'+this.pieceValue+'</div>';
			this.type = type;
			this.owner = owner;
		}

		/**
		 * @returns {HTMLElement} The HTMLElement that this tile wraps.
		 */
		ChessPiece.prototype.getElement = function() {
			return this.$element;
		}

		/**
		 * @returns {HTMLElement} The HTMLElement that this tile wraps.
		 */
		ChessPiece.prototype.getElementHTML = function() {
			return this.elementHTML;
		}

		window.ChessPiece = ChessPiece;
	})();

	// Start everything once the DOM is ready.
	$(document).ready(function() {
		console.log("We have a chess gentlemen.");
		var chess = new Chess($('#tiles'), 8, 8);
		chess.init();

		//chess.registerPlugin('ColorPalette', new ColorPalette());
		//chess.registerPlugin('FloodFill', new FloodFill());

		// For debugging purposes, make the instance available to the console.
		window.chess = chess;
	});
	</script>
</body>
</html>

