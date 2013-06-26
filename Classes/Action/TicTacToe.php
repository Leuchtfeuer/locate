<?php
namespace Bitmotion\Locate\Action;



/**
 * TicTacToe Action class
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Action
 */
class TicTacToe extends AbstractAction {

	/**
	 * Call the action module
	 *
	 * @param array $factsArray
	 * @param \Bitmotion\Locate\Judge\Decision
	 */
	public function Process(&$factsArray, &$decision)
	{
		die('<html>
<head>
<title>Tic Tac Toe</title>
<style>
center input { padding : 1em; font-size: 2em}

</style>
<script language="javascript" type="text/javascript">
	var myPointer, urPointer
	var gameStart = false
	var gameLevel
	var status = 1
	var startStatus = true

	mat = new Array(3)
	for (i = 0; i < mat.length; i++)
		mat[i] = new Array(3)

	for (i = 0; i < mat.length; i++) {
		for (j = 0; j < mat[i].length; j++)
			mat[i][j] = 0

	}

	function startIt() {
		gameStart = true
		if (startStatus != true) {
			alert("press clear button first")
			return

		}
		if (document.f1.pointer[0].checked == true) {
			urPointer = "   x   "
			myPointer = "   0   "
		} else {
			urPointer = "   0   "
			myPointer = "   x   "
		}

		if (document.f1.turn[1].checked == true)
			randomMoves(2)

		if (document.f1.level[0].checked == true)
			gameLevel = 0
		else if (document.f1.level[1].checked == true)
			gameLevel = 1
		else if (document.f1.level[2].checked == true)
			gameLevel = 2

		document.f1.pointer[0].disabled = true
		document.f1.pointer[1].disabled = true

		document.f1.turn[0].disabled = true
		document.f1.turn[1].disabled = true

		document.f1.level[0].disabled = true
		document.f1.level[1].disabled = true
		document.f1.level[2].disabled = true
	}

	function getInput(row, col) {

		if (status == 0) {
			alert("This game has ended. To start a new game, press clear, then start")
			return 0;
		}

		if (gameStart == false) {
			alert("Press start button to play the game")
			return



		}

		if (IsIndexEmpty(row, col) == false) {
			alert("This move is already taken")
			return

		}

		display(row, col, urPointer)
		mat[row][col] = 1

		if (winningConditions(1) == true) {
			alert("You win!")
			status = 0
			startStatus = false
			return

		}

		if (IsGameOver() == true) {
			alert("Game ended in a draw")
			status = 0
			startStatus = false
			return

		}

		myMoves()
		if (winningConditions(2) == true) {
			alert("you Lose!")
			status = 0
			startStatus = false
			return

		}

		if (IsGameOver() == true) {
			alert("Game ended in a draw")
			status = 0
			return

		}

	}

	function display(row, col, p) {
		if (row == 0 && col == 0)
			document.f1.b00.value = p
		else if (row == 0 && col == 1)
			document.f1.b01.value = p
		else if (row == 0 && col == 2)
			document.f1.b02.value = p
		else if (row == 1 && col == 0)
			document.f1.b10.value = p
		else if (row == 1 && col == 1)
			document.f1.b11.value = p
		else if (row == 1 && col == 2)
			document.f1.b12.value = p
		else if (row == 2 && col == 0)
			document.f1.b20.value = p
		else if (row == 2 && col == 1)
			document.f1.b21.value = p
		else if (row == 2 && col == 2)
			document.f1.b22.value = p
	}

	function IsIndexEmpty(row, col) {
		if (mat[row][col] != 0)
			return false
		else
			return true
	}

	function winningConditions(val) {
		if (mat[0][0] == val && mat[0][1] == val && mat[0][2] == val)
			return true
		else if (mat[1][0] == val && mat[1][1] == val && mat[1][2] == val)
			return true
		else if (mat[2][0] == val && mat[2][1] == val && mat[2][2] == val)
			return true
		else if (mat[0][0] == val && mat[1][0] == val && mat[2][0] == val)
			return true
		else if (mat[0][1] == val && mat[1][1] == val && mat[2][1] == val)
			return true
		else if (mat[0][2] == val && mat[1][2] == val && mat[2][2] == val)
			return true
		else if (mat[0][0] == val && mat[1][1] == val && mat[2][2] == val)
			return true
		else if (mat[0][2] == val && mat[1][1] == val && mat[2][0] == val)
			return true
		else
			return false
	}

	function IsGameOver() {
		for (i = 0; i < mat.length; i++) {
			for (j = 0; j < mat[i].length; j++) {
				if (mat[i][j] != 1 && mat[i][j] != 2)
					return false
			}

		}
		return true
	}

	function myMoves() {
		if (gameLevel == 0) {
			randomMoves(2)
			return

		}
		var bool = true
		bool = artificialIntelligence1(2, 2)
		if (bool == true)
			return



		bool = artificialIntelligence2(2, 2)
		if (bool == true)
			return



		bool = artificialIntelligence3(2, 2)
		if (bool == true)
			return



		bool = artificialIntelligence4(2, 2)
		if (bool == true)
			return





		bool = artificialIntelligence1(2, 1)
		if (bool == true)
			return



		bool = artificialIntelligence2(2, 1)
		if (bool == true)
			return

		bool = artificialIntelligence3(2, 1)
		if (bool == true)
			return



		bool = artificialIntelligence4(2, 1)
		if (bool == true)
			return



		if (gameLevel == 1) {
			randomMoves(2)
			return

		}
		bool = otherMoves(2, 1)
		if (bool == true)
			return



		randomMoves(2)

	}

	function artificialIntelligence1(myP, urP) //p for pointer: myp = 2, urp = 1
	{
		var count = 0
		for (i = 0; i < mat.length; i++) {
			for (j = 0; j < mat[i].length; j++) {
				if (mat[i][j] == urP)
					count++
			}
			if (count > 1) {
				for (j = 0; j < mat[i].length; j++) {
					if (IsIndexEmpty(i, j) == true) {
						mat[i][j] = myP
						display(i, j, myPointer)
						return true
					}
				}

			}
			count = 0
		}
		return false

	}

	function artificialIntelligence2(myP, urP) {
		var count = 0
		for (i = 0; i < mat.length; i++) {
			for (j = 0; j < mat[i].length; j++) {
				if (mat[j][i] == urP)
					count++
			}
			if (count > 1) {
				for (j = 0; j < mat[i].length; j++) {
					if (IsIndexEmpty(j, i) == true) {
						mat[j][i] = myP
						display(j, i, myPointer)
						return true
					}
				}

			}
			count = 0
		}
		return false
	}

	function artificialIntelligence3(myP, urP) {
		var count = 0
		for (i = 0; i < mat.length; i++) {
			if (mat[i][i] == urP)
				count++
		}

		if (count > 1) {
			for (i = 0; i < mat.length; i++) {
				if (IsIndexEmpty(i, i) == true) {
					mat[i][i] = myP
					display(i, i, myPointer)
					return true
				}

			}

		}
		return false

	}

	function artificialIntelligence4(myP, urP) {
		var count = 0
		var j = 2

		for (i = 0; i < mat.length; i++) {
			if (mat[i][j] == urP)
				count++
			j--

		}

		if (count > 1) {
			var j = 2
			for (i = 0; i < mat.length; i++) {
				if (IsIndexEmpty(i, j) == true) {
					mat[i][j] = myP
					display(i, j, myPointer)
					return true
				}
				j--

			}
		}
		return false

	}

	function otherMoves(myP, urP) {

		if (IsIndexEmpty(1, 1) == true) {
			mat[1][1] = myP
			display(1, 1, myPointer)
			return true
		}

		if (mat[1][1] == urP) {
			row = Math.floor(Math.random() * 2) * 2 //produces a rnd num of 0,2
			col = Math.floor(Math.random() * 2) * 2
			if (IsIndexEmpty(row, col) == true) {
				mat[row][col] = myP
				display(row, col, myPointer)
				return true
			}

		}

		return false

	}

	function randomMoves(myP) {

		row = Math.floor(Math.random() * 3)
		col = Math.floor(Math.random() * 3)
		while (IsIndexEmpty(row, col) != true) {
			row = Math.floor(Math.random() * 3)
			col = Math.floor(Math.random() * 3)
		}
		mat[row][col] = myP
		display(row, col, myPointer)
	}

	function clearMatrix() {

		document.f1.pointer[0].disabled = false
		document.f1.pointer[1].disabled = false

		document.f1.turn[0].disabled = false
		document.f1.turn[1].disabled = false

		document.f1.level[0].disabled = false
		document.f1.level[1].disabled = false
		document.f1.level[2].disabled = false

		status = 1
		startStatus = true
		gameStart = false

		for (i = 0; i < mat.length; i++) {
			for (j = 0; j < mat[i].length; j++)
				mat[i][j] = 0
		}

		document.f1.b00.value = "        "
		document.f1.b01.value = "        "
		document.f1.b02.value = "        "
		document.f1.b10.value = "        "
		document.f1.b11.value = "        "
		document.f1.b12.value = "        "
		document.f1.b20.value = "        "
		document.f1.b21.value = "        "
		document.f1.b22.value = "        "
	}
</script>
</head>
<body>


	<form name="f1" action="">

		<b>Choose your pointer </b><br> <input type="radio"
			name="pointer" value="x" checked>x <input type="radio"
			name="pointer" value="0">0
		<p>

			<b>Wanna play first or second? </b> <br> <input type="radio"
				name="turn" checked>first <input type="radio" name="turn">
			second
		</p>
		<p>

			<b>Choose your playing level </b> <br> <input type="radio"
				name="level">easy <input type="radio" name="level" checked>medium
			<input type="radio" name="level">hard
		</p>
		<p>

			<input type="button" name="bstart" value="  start  "
				onclick="startIt()"> <input type="button" name="bclear"
				value="  clear " onclick="clearMatrix()">


		</p>
		<center>
			<input type="button" name="b00" value="        "
				onclick="getInput(0,0)"> <input type="button" name="b01"
				value="        " onclick="getInput(0,1)"> <input
				type="button" name="b02" value="        " onclick="getInput(0,2)"><br>
			<input type="button" name="b10" value="        "
				onclick="getInput(1,0)"> <input type="button" name="b11"
				value="        " onclick="getInput(1,1)"> <input
				type="button" name="b12" value="        " onclick="getInput(1,2)"><br>
			<input type="button" name="b20" value="        "
				onclick="getInput(2,0)"> <input type="button" name="b21"
				value="        " onclick="getInput(2,1)"> <input
				type="button" name="b22" value="        " onclick="getInput(2,2)"><br>
		</center>

	</form>


</body>
</html>');
	}

}

