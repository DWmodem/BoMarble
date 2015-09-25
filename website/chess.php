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
		<h1></h1>
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

				//Quick definition of the colors.
				this.highlightGreen = "#66FF99";
				this.highlightYellow = "#CCCC66";
				this.blackTile = "lightgrey";
				this.whiteTile = "white";

				this.container = $(container);

				this.tilesByXY = [];	// <-- Board in memory.
				this.tilesByID = {};

				this.width = width;
				this.height = height;

				this.plugins = {};
			}
			Chess.prototype.gameStart = function() {
				console.log("game start");
				this.recalculateAllMoves();
				
			};
			
			Chess.prototype.recalculateAllMoves = function() {
				board = this.tilesByXY;
				console.log(board);
				console.log(board.length);
				for(var x = 0; x < board.length; x++){
					for(var y = 0; y < board[x].length; y++){
						piece = board[x][y].getPiece();
						console.log(piece);
						if(piece != null){
							piece.calculateMoves();
							console.log("("+x+", "+y+") valid moves: "+piece.getValidMoves());
							console.log("("+x+", "+y+") valid eats: "+piece.getValidEats());
						}
					}
				}
			};			
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
					alternateColors = [this.whiteTile, this.blackTile];
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
					var id = $(this).prop("id");
					id = id.replace(/[tile-]/g, '');
					console.log("Mouseenter");
					console.log(id);
					tile = self.getTile(id.charAt(0), id.charAt(1));
					piece = self.getPiece(id.charAt(0), id.charAt(1));
					self.highlightMoves(piece);
					self.highlightEats(piece);
				});

				this.container.on('mouseleave', '.tile', function(e) {
					var id = $(this).prop("id");
					id = id.replace(/[tile-]/g, '');

					console.log("Mouseleave");

					tile = self.getTile(id.charAt(0), id.charAt(1));
					piece = self.getPiece(id.charAt(0), id.charAt(1));
					self.revertHighlights(piece);
					console.log(piece);
					console.log(tile);
				});
			};

			Chess.prototype.putPiece = function(owner, archetype, x, y){
				console.log("~~~put piece~~~");
				console.log("Owner: "+owner.id);
				tile = this.getTile(x, y);
				type = new archetype(tile, owner);
				console.log("Type Owner: "+type.owner.id);
				console.log(type);
				piece = new ChessPiece(type, owner);
				tile.setPiece(piece);
				console.log("~~~put piece~~~");
			};

			Chess.prototype.getTile = function(x, y){
				return this.tilesByXY[x][y];
			};

			Chess.prototype.getPiece = function(x, y){
				return this.getTile(x, y).getPiece();
			};
			
			//Moves is an array of tuples. the first element is x, and the second y.
			Chess.prototype.highlightMoves = function(piece){
				
				if(piece == null){
					console.log("Piece is null");
					return;
				}

				moves = piece.getValidMoves();
				console.log("got valid moves. Length: "+moves.length);
				for(var i = 0; i < moves.length; i++){
					var hx = moves[i][0];	// <--- moves[i] is the tuple. moves[i][0] is the x
					var hy = moves[i][1];
					console.log("Setting "+hx+", "+hy+" to green");
				this.tilesByXY[hx][hy].setColor(this.highlightGreen);
				}
			};

			Chess.prototype.highlightEats = function(piece){
				
				if(piece == null){
					console.log("Piece is null");
					return;
				}

				moves = piece.getValidEats();
				console.log("got valid eats. Length: "+moves.length);
				for(var i = 0; i < moves.length; i++){
					var hx = moves[i][0];	// <--- moves[i] is the tuple. moves[i][0] is the x
					var hy = moves[i][1];
					console.log("Setting "+hx+", "+hy+" to yellow");
				this.tilesByXY[hx][hy].setColor(this.highlightYellow);
				}
			};			
			Chess.prototype.revertHighlights = function(piece){
				
				if(piece == null){
					console.log("Piece is null");
					return;
				}

				moves = piece.getValidMoves();
				eats = piece.getValidEats();
				moves = moves.concat(eats);
				console.log("got valid moves. Length: "+moves.length);
				for(var i = 0; i < moves.length; i++){
					var hx = moves[i][0];	// <--- moves[i] is the tuple. moves[i][0] is the x
					var hy = moves[i][1];
					this.tilesByXY[hx][hy].setPrevColor();
					
				}
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
			this.piece = null;
			this.$element = $('<div class="tile">');
			this.$element.prop('id', 'tile-' + x + '-' + y);
			this.x = x;
			this.y = y;
			this.previousColor = color;	//We need to remember the color. We change it when pieces move.
			this.setColor(color);
		}
		/**
		 * @returns {bool} true if the tile has no piece in it. False if it does.
		 */
		ChessTile.prototype.isEmpty = function() {
			return (this.piece == null);
		}
		/**
		 * @returns {bool} 
		 */
		ChessTile.prototype.hasDiffOwner = function(otherTile) {
			console.log("Tile: x="+tile.x+" y="+tile.y);
			console.log("otherTile: x="+otherTile.x+" y="+otherTile.y);
			console.log("Owner 1: "+this.getOwner().id);
			console.log("Owner 2: "+otherTile.getOwner().id);
			console.log("has diff owner? return "+!(this.getOwner().id == otherTile.getOwner().id));
			return !(this.getOwner().id == otherTile.getOwner().id);
		}

		/**
		 * @returns {player} The player that owns this tile.
		 */
		ChessTile.prototype.getOwner = function() {
			if(this.isEmpty()){
				return [];
			}
			return this.piece.owner;
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

		ChessTile.prototype.setBorderStyle = function(color) {
			this.$element[0].style.borderColor = color;
		};
		
		ChessTile.prototype.revertBorderStyle = function(color) {
			this.$element[0].style.borderColor = color;
		};
		ChessTile.prototype.setPrevColor = function(){
			this.color = this.previousColor;
			this.$element[0].style.backgroundColor = this.color;
		};

		ChessTile.prototype.setPiece = function(piece) {
			this.piece = piece;
			this.$element.append(this.piece.getElement());
			return;
		};
		
		ChessTile.prototype.getPiece = function() {
			return this.piece;
		};		

		window.ChessTile = ChessTile;
	})();

		(function() {

		var ChessPiece = function(type, owner) {
			this.type = type;
			this.$element = '<div class="piece">'+this.type.getAppearance()+'</div>';
			this.owner = owner;
			this.validMoves = [];
			this.validEats = [];

			this.type.setPiece(this);
			this.type.calculateValidMoves();
		}

		/**
		 * @returns {HTMLElement} The HTMLElement that this tile wraps.
		 */
		ChessPiece.prototype.getElement = function() {
			return this.$element;
		}

		ChessPiece.prototype.calculateMoves = function() {
			this.type.calculateValidMoves();
		}
		
		ChessPiece.prototype.getValidMoves = function() {
			return this.validMoves;
		}

		ChessPiece.prototype.getValidEats = function() {
			return this.validEats;
		}
		window.ChessPiece = ChessPiece;
	})();

		(function() {

		var Rook = function(tile, owner) {
			this.validMoves = [];
			this.validEats = [];
			this.board = window.chess.tilesByXY;
			this.width = window.chess.width;
			this.height = window.chess.height;
			this.appearance = owner.getRookMien();
			this.tilex = 0;
			this.tiley = 0;
			this.owner = owner;
			this.setTile(tile);
			this.tile = tile;
		}

		Rook.prototype.setTile = function(tile){
			this.tilex = tile.getX();
			this.tiley = tile.getY();
		}
		Rook.prototype.setPiece = function(piece){
			this.piece = piece;
		}

		Rook.prototype.calculateValidMoves = function(){
			newMoves = [];
			newEats = [];
			var i = 0;
			var j = 0;
			tiley = this.tiley;
			tilex = this.tilex;
			height = this.height;
			width = this.width;
			board = this.board;
			rookleft = function(){
				for(j = tilex-1; j >= 0; j--){
					if(board[j][tiley].isEmpty()){		//If the tile is empty
						newMoves.push([j, tiley]);
					} else {
						if(board[j][tiley].hasDiffOwner(chess.getTile(tilex, tiley))){
							console.log("For ("+tilex+", "+tiley+"), ("+tilex+", "+j+") is an edible piece");
							newEats.push([j, tiley]);
						}
						break;
					}
				}
			}

			rookright = function(){
				for(j = tilex+1; j < width; j++){
					if(board[j][tiley].isEmpty()){		//If the tile is empty
						newMoves.push([j, tiley]);
					} else {
						if(board[j][tiley].hasDiffOwner(chess.getTile(tilex, tiley))){
							console.log("For ("+tilex+", "+tiley+"), ("+tilex+", "+j+") is an edible piece");
							newEats.push([j, tiley]);
						}
						break;
					}
				}
			}

			rookup = function(){
				for(i = tiley-1; i >= 0; i--){
					if(board[tilex][i].isEmpty()){
						newMoves.push([tilex, i]);

					} else {
						if(board[tilex][i].hasDiffOwner(chess.getTile(tilex, tiley))){
							console.log("For ("+tilex+", "+tiley+"), ("+tilex+", "+i+") is an edible piece");
							newEats.push([tilex, i]); //add the piece if it is an enemy piece
						}
						break;	//Do not continue, rooks cannot eat past pieces.
					}
				}
			}

			rookdown = function(){
				for(i = tiley+1; i < height; i++){
					if(board[tilex][i].isEmpty()){		//If the tile is empty
						newMoves.push([tilex, i]);

					} else {
						if(board[tilex][i].hasDiffOwner(chess.getTile(tilex, tiley))){
							console.log("For ("+tilex+", "+tiley+"), ("+tilex+", "+i+") is an edible piece");
							newEats.push([tilex, i]); //add the piece if it is an enemy piece
						}
						break;	//Do not continue, rooks cannot eat past pieces.
					}
				}
			}
			rookleft();
			rookup();
			rookdown();
			rookright();

			this.piece.validMoves = newMoves;
			this.piece.validEats = newEats;
		}

		Rook.prototype.getAppearance = function(){
			return this.appearance;
		}

		window.Rook = Rook;
	})();

(function() {

		var Player = function(id, pieces){
			this.id = id;
			this.pieceSet = pieces;
		}

		Player.prototype.getID = function(){
			return this.id;
		}

		Player.prototype.getRookMien = function(){
			return this.pieceSet["rook"];
		}
		
		window.Player = Player;
	})();

	// Start everything once the DOM is ready.
	$(document).ready(function() {
		var player1 = new Player("54F63E2895623478", {rook: "&#9814"});
		var player2 = new Player("spoopy", {rook: "&#9820"});


		console.log("We have a chess gentlemen.");
		var chess = new Chess($('#tiles'), 8, 8);
		chess.init();
		//chess.registerPlugin('ColorPalette', new ColorPalette());
		//chess.registerPlugin('FloodFill', new FloodFill());

		// For debugging purposes, make the instance available to the console.
		window.chess = chess;
		chess.putPiece(player1, Rook, 1, 4);
		chess.putPiece(player2, Rook, 1, 6);
		chess.putPiece(player2, Rook, 3, 1);
		chess.putPiece(player1, Rook, 3, 4);
		chess.putPiece(player1, Rook, 6, 1);
		chess.putPiece(player1, Rook, 3, 6);
		chess.gameStart();
	});
	</script>
</body>
</html>

