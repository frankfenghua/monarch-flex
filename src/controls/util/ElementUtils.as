package controls.util
{
	public class ElementUtils
	{
		public static function getX(elem:Object):int {
			var x:int = 0;
			while(elem) {
				x += elem.offsetLeft;
				elem = elem.offsetParent;
			}	
			return x;
		}
		
		public static function getY(elem:Object):int {
			var y:int = 0;
			while(elem) {
				y += elem.offsetTop;
				elem = elem.offsetParent;
			}	
			return y;
		}
		
		public static function elementOverlapsVerticalBounds(elem:Object, top:int, bottom:int):Boolean {
			var topEdge:int = getY(elem);
	        var bottomEdge:int = getY(elem) + elem.height;
	        	
        	if((topEdge >= top && topEdge <= bottom) || (bottomEdge >= top && bottomEdge <= bottom)) {
        		return true;
    		}
    	   	else {
    	   		return false;
    	   	}
	 	}
	 	
	 	public static function elementOverlapsHorizontalBounds(elem:Object, left:int, right:int):Boolean {
			var leftEdge:int = getX(elem);
	        var rightEdge:int = getX(elem) + elem.width;
	        	
        	if((leftEdge >= left && leftEdge <= right) || (rightEdge >= left && rightEdge <= right)) {
        		return true;
    		}
    	   	else {
    	   		return false;
    	   	}
	 	}
	}
}