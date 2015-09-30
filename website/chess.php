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

		#tiles {
			min-width: 775px;
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
			var Chess = function(container, width, height, players){

				//Quick definition of the colors.
				this.highlightGreen = "#66FF99";
				this.highlightYellow = "#CCCC66";
				this.selectionBlue = "lightblue";
				this.blackTile = "lightgrey";
				this.whiteTile = "white";
				this.players = players;
				this.container = $(container);

				this.tilesByXY = [];	// <-- Board in memory.
				this.tilesByID = {};

				this.width = width;
				this.height = height;

				this.white = players[0];
				this.black = players[1];
				this.activePlayer;
				this.otherPlayer;
				this.plugins = {};
			}
			Chess.prototype.gameStart = function(white, black) {
				console.log("game start");
				this.activePlayer = white;
				this.otherPlayer = black;
				this.recalculateAllMoves();
			};
			
			Chess.prototype.setupBoard = function(){

				//White
				chess.putPiece(this.white, Rook, 7, 7);
				chess.putPiece(this.white, Rook, 0, 7);
				chess.putPiece(this.white, Knight, 1, 7);
				chess.putPiece(this.white, Knight, 6, 7);
				chess.putPiece(this.white, Fool, 2, 7);
				chess.putPiece(this.white, Fool, 5, 7);
				chess.putPiece(this.white, Queen, 3, 7);
				chess.putPiece(this.white, King, 4, 7);

				chess.putPiece(this.white, Pawn, 0, 6);
				chess.putPiece(this.white, Pawn, 1, 6);
				chess.putPiece(this.white, Pawn, 2, 6);
				chess.putPiece(this.white, Pawn, 3, 6);
				chess.putPiece(this.white, Pawn, 4, 6);
				chess.putPiece(this.white, Pawn, 5, 6);
				chess.putPiece(this.white, Pawn, 6, 6);
				chess.putPiece(this.white, Pawn, 7, 6);

				//Black
				chess.putPiece(this.black, Pawn, 0, 1);
				chess.putPiece(this.black, Pawn, 1, 1);
				chess.putPiece(this.black, Pawn, 2, 1);
				chess.putPiece(this.black, Pawn, 3, 1);
				chess.putPiece(this.black, Pawn, 4, 1);
				chess.putPiece(this.black, Pawn, 5, 1);
				chess.putPiece(this.black, Pawn, 6, 1);
				chess.putPiece(this.black, Pawn, 7, 1);

				chess.putPiece(this.black, King, 4, 0);
				chess.putPiece(this.black, Queen, 3, 0);
				chess.putPiece(this.black, Fool, 2, 0);
				chess.putPiece(this.black, Fool, 5, 0);
				chess.putPiece(this.black, Knight, 1, 0);
				chess.putPiece(this.black, Knight, 6, 0);
				chess.putPiece(this.black, Rook, 0, 0);
				chess.putPiece(this.black, Rook, 7, 0);

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

			Chess.prototype.setActivePlayer = function(player){

				this.activePlayer.setTurn(false);
				this.activePlayer = player;
				this.activePlayer.setTurn(true);

			}

			//Change this to handle x number of players
			Chess.prototype.turnOver = function(){
				this.checkWinConditions();	//End the game if a win condition is met. (a king is under check and has no valid moves/valid eats)

				var swp;
				swp = this.activePlayer;
				this.activePlayer.setTurn(false);
				this.activePlayer = this.otherPlayer;
				this.otherPlayer = swp;
				this.activePlayer.setTurn(true);
				console.log("Turn over");
			}

			Chess.prototype.checkWinConditions = function(){
				console.log("Checking win conditions...");
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

				//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~Controls the mouseovers
				this.container.on('mouseenter', '.tile', function(e) {
					var id = $(this).prop("id");
					id = id.replace(/[tile-]/g, '');
					console.log("Mouseenter");
					console.log(id);
					var tile = self.getTile(id.charAt(0), id.charAt(1));
					var piece = self.getPiece(id.charAt(0), id.charAt(1));

					//Don't bother for empty tiles
					if(piece == null){
						return;
					}
					//Only highlight your own pieces
					//Only highlight pieces if activePlayer has no selection made
					if((self.activePlayer == piece.getOwner()) && (piece.getOwner().getSelection() == null)) {
						self.highlightMoves(piece);
						self.highlightEats(piece);
					}
				});

				this.container.on('mouseleave', '.tile', function(e) {
					
					if(self.activePlayer.hasSelection()){
						return;
					}

					var id = $(this).prop("id");
					id = id.replace(/[tile-]/g, '');

					console.log("Mouseleave");

					var tile = self.getTile(id.charAt(0), id.charAt(1));
					var piece = self.getPiece(id.charAt(0), id.charAt(1));

					//Only revertHighlight your own pieces
					//Only revertHighlight pieces if activePlayer has no selection made (Nothing should happen when a selection is made!!!)
					if((self.activePlayer == piece.getOwner()) && (piece.getOwner().getSelection() == null)) {
						self.revertHighlights(piece);
					}

				});
				//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~Controls the mouseovers
				//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~Controls the clicks
				this.container.on('click', '.tile', function(e) {
					var id = $(this).prop("id");
					id = id.replace(/[tile-]/g, '');

					var piece = self.getPiece(id.charAt(0), id.charAt(1));
					var tile = self.getTile(id.charAt(0), id.charAt(1));
					if(piece != null){
						var owner = piece.getOwner();
					}
					//Move
					if((self.activePlayer.getSelection() != null) && (self.activePlayer.getSelection().looksAt(tile))){
						var movingPiece = self.activePlayer.getSelection();
						self.revertSelectionHighlight(movingPiece);
						self.revertHighlights(movingPiece);
						tile.setPiece(movingPiece);
						movingPiece.getTile().updatePiece(null);

						self.activePlayer.deselect();
						movingPiece.moveTo(tile);
						self.recalculateAllMoves();
						console.log("legal move");

						//Because when we click on a tile the mouse is there, we expect to see the highlights.
						// self.highlightMoves(movingPiece);
						// self.highlightEats(movingPiece);
						//Don't need this anymore since we alternate turns now
						self.turnOver();
						return;

					//Selection
					} else if(self.activePlayer == owner && (self.activePlayer.getSelection() != piece)) {		//If the active player owns this piece, and the player hasn't already selected this piece
						self.revertSelectionHighlight(self.activePlayer.getSelection());
						self.revertHighlights(self.activePlayer.getSelection());

						self.activePlayer.deselect();
						self.activePlayer.selectPiece(piece);
						self.selectionHighlight(piece);
						self.highlightMoves(piece);
						self.highlightEats(piece);
						console.log("Piece selected");
						return;

					//Deselection.
					} else if(self.activePlayer.getSelection() == piece){	//If the active player has this piece as her selection
						self.activePlayer.deselect();
						self.revertSelectionHighlight(piece);
						console.log("Piece deselected");
						return;

					//eat and move
					} else if((piece != null) && (self.activePlayer.getSelection() != null) && (self.activePlayer.getSelection().targets(piece))){	//If the target (piece) can be found in the validEats of the activePiece.
						self.activePlayer.eat(piece);	//Adds to its captures

						var movingPiece = self.activePlayer.getSelection();	//The moving piece is the one that is previously selected
						self.revertSelectionHighlight(movingPiece);
						self.revertHighlights(movingPiece);
						tile.setPiece(movingPiece);
						movingPiece.getTile().updatePiece(null);

						self.activePlayer.deselect();
						movingPiece.moveTo(tile);
						self.recalculateAllMoves();
						console.log("eat and move");
						self.turnOver();
						return;
					}
					console.log("Not an expected case");
					return;

				});
				//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~Controls the clicks
			};

			Chess.prototype.putPiece = function(owner, archetype, x, y, hasMoved){
				tile = this.getTile(x, y);
				type = new archetype(tile, owner);
				piece = new ChessPiece(type, owner, hasMoved);
				tile.setPiece(piece);
				owner.addPiece(piece);
			};

			Chess.prototype.getTile = function(x, y){
				return this.tilesByXY[x][y];
			};

			Chess.prototype.getPiece = function(x, y){
				return this.getTile(x, y).getPiece();
			};
			
			Chess.prototype.getOwner = function(x, y){
				return this.getTile(x, y).getPiece().getOwner();
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
				console.log(piece);
				console.log(moves);
				for(var i = 0; i < moves.length; i++){
					var hx = moves[i][0];	// <--- moves[i] is the tuple. moves[i][0] is the x
					var hy = moves[i][1];
					console.log("Setting "+hx+", "+hy+" to yellow");
					this.tilesByXY[hx][hy].setColor(this.highlightYellow);
				}
			};

			Chess.prototype.selectionHighlight = function(piece){
					this.tilesByXY[piece.getX()][piece.getY()].setBorderStyle(this.selectionBlue);
			}

			Chess.prototype.revertSelectionHighlight = function(piece){
				if(!(piece == null)){
					this.tilesByXY[piece.getX()][piece.getY()].revertBorderStyle();
				}
			}
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
			this.previousStyle = [];
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
			this.$element[0].style.backgroundColor = color;
		};
		
		ChessTile.prototype.revertBorderStyle = function() {
			this.$element[0].style.backgroundColor = this.previousColor;
		};

		ChessTile.prototype.setPrevColor = function(){
			this.$element[0].style.backgroundColor = this.previousColor;
		};

		ChessTile.prototype.setPiece = function(piece) {
			this.piece = piece;
			this.$element.empty();
			if(piece != null){
				this.$element.append(this.piece.getElement());
			}
			return;
		};

		ChessTile.prototype.updatePiece = function(piece){
			this.setPiece(piece);
			this.$element.empty();
			if(piece != null){
				this.$element.append(this.piece.getElement());
			}
		}

		ChessTile.prototype.getPiece = function() {
			return this.piece;
		};		

		window.ChessTile = ChessTile;
	})();

		(function() {

		var ChessPiece = function(type, owner, hasMoved) {
			this.type = type;
			this.$element = '<div class="piece">'+this.type.getAppearance()+'</div>';
			this.owner = owner;
			console.log("hasMoved passed is: "+hasMoved);
			this.hasMoved = false;
			if(hasMoved != null){
				console.log("hasMoved was passed and is: "+hasMoved);
				this.hasMoved = hasMoved;
			}
			this.validMoves = [];
			this.validEats = [];

			this.type.setPiece(this);
			//this.type.calculateValidMoves();
		}

		/**
		 * @returns {HTMLElement} The HTMLElement that this tile wraps.
		 */
		ChessPiece.prototype.getElement = function() {
			return this.$element;
		}
		ChessPiece.prototype.getTile = function(){
			return this.type.tile;	
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
		ChessPiece.prototype.moveTo = function(tile){
			this.type.setTile(tile);
			this.hasMoved = true;
			this.type.calculateValidMoves();
		}

		ChessPiece.prototype.targets = function(piece){
			var px = piece.getX();
			var py = piece.getY();
			var eats = this.validEats;
			for(var i = 0; i < eats.length; i++){
				console.log("eats[i] = "+eats[i]);
				console.log("[px, py] = ["+px+", "+py+"]");
				if((eats[i][0] == px) && (eats[i][1] == py)){
					return true;
				}
			}
			return false;
		}
		ChessPiece.prototype.looksAt = function(tile){
			console.log(tile);
			var tx = tile.getX();
			var ty = tile.getY();
			var moves = this.validMoves;
			for(var i = 0; i < moves.length; i++){
				console.log("moves[i] = "+moves[i]);
				console.log("[tx, ty] = ["+tx+", "+ty+"]");
				if((moves[i][0] == tx) && (moves[i][1] == ty)){
					return true;
				}
			}
			return false;			
		}

		ChessPiece.prototype.getOwner = function() {
			return this.owner;
		}

		ChessPiece.prototype.getX = function(){
			return this.type.tilex;	
		}

		ChessPiece.prototype.getY = function(){
			return this.type.tiley;
		}		
		window.ChessPiece = ChessPiece;
	})();
	(function() {

		var Pawn = function(tile, owner) {
			this.validMoves = [];
			this.validEats = [];
			this.board = window.chess.tilesByXY;
			this.width = window.chess.width;
			this.height = window.chess.height;
			this.appearance = owner.getPawnMien();
			this.tilex = 0;
			this.tiley = 0;
			this.owner = owner;
			this.setTile(tile);
			this.tile = tile;
			this.facing = owner.facing;
		}

		Pawn.prototype.setTile = function(tile){
			this.tile = tile;
			this.tilex = tile.getX();
			this.tiley = tile.getY();
		}
		Pawn.prototype.setPiece = function(piece){
			this.piece = piece;
		}

		Pawn.prototype.calculateValidMoves = function(){
			newMoves = [];
			newEats = [];
			var i = 0;
			var j = 0;
			var one = 1;
			tiley = this.tiley;
			tilex = this.tilex;
			height = this.height;
			width = this.width;
			board = this.board;
			console.log("This piece is facing: "+this.facing);

			//Black pawns move down, white pawns move up.
			if(this.facing == "down"){
				one = -1;
			}

			//Diagonal tiles are not empty and the piece there has a different owner.
			if(	((tiley-one) >= 0) &&
			 	((tilex+one) >= 0) &&
				((tiley-one) < height) &&
			 	((tilex+one) < width) &&
			  	(!board[tilex+one][tiley-one].isEmpty()) &&
			   	(board[tilex+one][tiley-one].hasDiffOwner(chess.getTile(tilex, tiley))) ) {
				
					newEats.push([(tilex+one), (tiley-one)]);
			}

			if(	((tiley-one) >= 0) &&
				((tiley-one) < height) &&
			 	((tilex-one) >= 0) &&
			 	((tilex-one) < width) &&
			 	(!board[tilex-one][tiley-one].isEmpty()) &&
			 	(board[tilex-one][tiley-one].hasDiffOwner(chess.getTile(tilex, tiley))) ) {
				
					newEats.push([(tilex-one), (tiley-one)]);
			}

			//Forwards once, or forwards twice.
			if(	((tiley-one) >= 0) &&
				((tiley-one) < height) &&
				board[tilex][tiley-one].isEmpty()) {
					
					newMoves.push([tilex, (tiley-one)]);
					if(	((tiley-one-one) >= 0) &&
						((tiley-one-one) < height) &&
						!chess.getTile(tilex, tiley).getPiece().hasMoved &&
						board[tilex][tiley-one-one].isEmpty()){
							
							newMoves.push([tilex, (tiley-one-one)]);
					}
			}

			this.piece.validMoves = newMoves;
			this.piece.validEats = newEats;
		}

		Pawn.prototype.getAppearance = function(){
			return this.appearance;
		}

		window.Pawn = Pawn;
	})();
		(function() {

		var King = function(tile, owner) {
			this.validMoves = [];
			this.validEats = [];
			this.board = window.chess.tilesByXY;
			this.width = window.chess.width;
			this.height = window.chess.height;
			this.appearance = owner.getKingMien();
			this.tilex = 0;
			this.tiley = 0;
			this.owner = owner;
			this.setTile(tile);
			this.tile = tile;
		}

		King.prototype.setTile = function(tile){
			this.tile = tile;
			this.tilex = tile.getX();
			this.tiley = tile.getY();
		}
		King.prototype.setPiece = function(piece){
			this.piece = piece;
		}

		King.prototype.calculateValidMoves = function() {
			newMoves = [];
			newEats = [];
			var i = 0;
			var j = 0;
			tiley = this.tiley;
			tilex = this.tilex;
			height = this.height;
			width = this.width;
			board = this.board;

			//Moves up
			if(	((tiley-1) >= 0)){
				if(board[tilex][tiley-1].isEmpty()){
					newMoves.push([tilex, (tiley-1)]);

				} else if(board[tilex][tiley-1].hasDiffOwner(this.tile)){
					newEats.push([tilex, (tiley-1)]);
				}
			}
			//Moves down			
			if(	((tiley+1) < height)){
				if(board[tilex][tiley+1].isEmpty()){
					newMoves.push([tilex, tiley+1]);

				} else if(board[tilex][tiley+1].hasDiffOwner(this.tile)){
					newEats.push([tilex, tiley+1]);
				}
			}

			//Moves left 
			if(	((tilex-1) >= 0)){
				if(board[tilex-1][tiley].isEmpty()){
					newMoves.push([tilex-1, tiley]);
					
				} else if(board[tilex-1][tiley].hasDiffOwner(this.tile)){
					newEats.push([tilex-1, tiley]);
				}
			}

			//Moves right
			if(	((tilex+1) < width)){
				if(board[tilex+1][tiley].isEmpty()){
					newMoves.push([tilex+1, tiley]);

				} else if(board[tilex+1][tiley].hasDiffOwner(this.tile)){
					newEats.push([tilex+1, tiley]);
				}
			}

			//Moves up-left
			if(	((tiley-1) >= 0) &&
				((tilex-1) >= 0)){
				if(board[tilex-1][tiley-1].isEmpty()) {
					newMoves.push([tilex-1, tiley-1]);

				} else if(board[tilex-1][tiley-1].hasDiffOwner(this.tile)){
					newEats.push([tilex-1, tiley-1]);
				}
			}
			//Moves down-right
			if(	((tiley+1) < height) &&
				((tilex+1) < width)){
				if(board[tilex+1][tiley+1].isEmpty()){
					newMoves.push([tilex+1, (tiley+1)]);

				} else if(board[tilex+1][tiley+1].hasDiffOwner(this.tile)){
					newEats.push([tilex+1, tiley+1]);
				}
			}
			//Moves down-left
			if(	((tilex-1) >= 0) &&
				((tiley+1) < height)){
				if(board[tilex-1][tiley+1].isEmpty()){
					newMoves.push([tilex-1, tiley+1]);
				} else if(board[tilex-1][tiley+1].hasDiffOwner(this.tile)){
					newEats.push([tilex-1, tiley+1]);
				}
			}

			//Moves up-right
			if(	((tilex+1) < width) &&
				((tiley-1) >= 0)) {
				if(board[tilex+1][tiley-1].isEmpty()){
					newMoves.push([tilex+1, tiley-1]);
				} else if(board[tilex+1][tiley-1].hasDiffOwner(this.tile)){
					newEats.push([tilex+1, tiley-1]);
				}
			}

			this.piece.validMoves = newMoves;
			this.piece.validEats = newEats;
		}

		King.prototype.getAppearance = function(){
			return this.appearance;
		}

		window.King = King;
	})();
		(function() {

		var Queen = function(tile, owner) {
			this.validMoves = [];
			this.validEats = [];
			this.board = window.chess.tilesByXY;
			this.width = window.chess.width;
			this.height = window.chess.height;
			this.appearance = owner.getQueenMien();
			this.tilex = 0;
			this.tiley = 0;
			this.owner = owner;
			this.setTile(tile);
			this.tile = tile;
		}

		Queen.prototype.setTile = function(tile){
			this.tile = tile;
			this.tilex = tile.getX();
			this.tiley = tile.getY();
		}
		Queen.prototype.setPiece = function(piece){
			this.piece = piece;
		}

		Queen.prototype.calculateValidMoves = function(){
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

		Queen.prototype.getAppearance = function(){
			return this.appearance;
		}

		window.Queen = Queen;
	})();
		(function() {

		var Fool = function(tile, owner) {
			this.validMoves = [];
			this.validEats = [];
			this.board = window.chess.tilesByXY;
			this.width = window.chess.width;
			this.height = window.chess.height;
			this.appearance = owner.getFoolMien();
			this.tilex = 0;
			this.tiley = 0;
			this.owner = owner;
			this.setTile(tile);
			this.tile = tile;
		}

		Fool.prototype.setTile = function(tile){
			this.tile = tile;
			this.tilex = tile.getX();
			this.tiley = tile.getY();
		}
		Fool.prototype.setPiece = function(piece){
			this.piece = piece;
		}

		Fool.prototype.calculateValidMoves = function(){
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

		Fool.prototype.getAppearance = function(){
			return this.appearance;
		}

		window.Fool = Fool;
	})();
		(function() {

		var Knight = function(tile, owner) {
			this.validMoves = [];
			this.validEats = [];
			this.board = window.chess.tilesByXY;
			this.width = window.chess.width;
			this.height = window.chess.height;
			this.appearance = owner.getKnightMien();
			this.tilex = 0;
			this.tiley = 0;
			this.owner = owner;
			this.setTile(tile);
			this.tile = tile;
		}

		Knight.prototype.setTile = function(tile){
			this.tile = tile;
			this.tilex = tile.getX();
			this.tiley = tile.getY();
		}
		Knight.prototype.setPiece = function(piece){
			this.piece = piece;
		}

		Knight.prototype.calculateValidMoves = function(){
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

		Knight.prototype.getAppearance = function(){
			return this.appearance;
		}

		window.Knight = Knight;
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
			this.tile = tile;
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

		var Player = function(id, piecesMien, facing){
			this.id = id;
			this.pieceSetMien = piecesMien;	//Array of miens (unicode chars for chess pieces)
			this.activePieces = [];			//Also a pieces array.	it says "active pieces" but we'll use this to refer to all pieces for now
			this.captures = [];				//Pieces array.
			this.selection = null;			//Needs to be null at times.
			this.turn = false;
			this.facing = facing;
		}
		Player.prototype.addPiece = function(piece){
			this.activePieces.push(piece);
		}

		Player.prototype.eat = function(piece){
			this.captures.push(piece);
		}

		Player.prototype.selectPiece = function(piece){
				this.selection = piece;
				console.log("Piece selected.");
		}

		Player.prototype.getSelection = function(){
			return this.selection;
		}

		Player.prototype.deselect = function(){
			this.selection = null;
		}

		Player.prototype.getID = function(){
			return this.id;
		}

		Player.prototype.getRookMien = function(){
			return this.pieceSetMien["rook"];
		}
		
		Player.prototype.getKnightMien = function(){
			return this.pieceSetMien["knight"];
		}
		
		Player.prototype.getFoolMien = function(){
			return this.pieceSetMien["fool"];
		}
		
		Player.prototype.getQueenMien = function(){
			return this.pieceSetMien["queen"];
		}
		
		Player.prototype.getKingMien = function(){
			return this.pieceSetMien["king"];
		}

		Player.prototype.getPawnMien = function(){
			return this.pieceSetMien["pawn"];
		}

		Player.prototype.addActivePiece = function(piece){
			this.activePieces.push(piece);
		}

		Player.prototype.hasSelection = function(){
			return !(this.selection == null);
		}

		Player.prototype.setTurn = function(isTurn){
			this.turn = isTurn;
		}

		Player.prototype.isTurn = function(){
			return this.turn;
		}
		window.Player = Player;
	})();

	// Start everything once the DOM is ready.
	$(document).ready(function() {
		var whitePieces = {rook: "&#9814", knight: "&#9816", fool: "&#9815", queen: "&#9813", king: "&#9812", pawn: "&#9817"};
		var blackPieces = {rook: "&#9820", knight: "&#9822", fool: "&#9821", queen: "&#9819", king: "&#9818", pawn: "&#9823"};
		var player1 = new Player("54F63E2895623478", whitePieces, "up");
		var player2 = new Player("spoopy", blackPieces, "down");

		console.log("We have a chess gentlemen.");
		var chess = new Chess($('#tiles'), 8, 8, [player1, player2]);
		chess.init();
		//chess.registerPlugin('ColorPalette', new ColorPalette());
		//chess.registerPlugin('FloodFill', new FloodFill());

		// For debugging purposes, make the instance available to the console.
		window.chess = chess;
		chess.setupBoard();

		// chess.putPiece(player1, Pawn, 5, 4, true);
		// chess.putPiece(player2, Rook, 6, 5);
		// chess.putPiece(player2, Pawn, 6, 4, true);

		chess.gameStart(player1, player2);
	});
	</script>
</body>
</html>

