/*
Copyright (c) 2007 FlexLib Contributors.  See:
    http://code.google.com/p/flexlib/wiki/ProjectContributors

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
package controls.textClasses
{
	import flash.geom.Rectangle;
	import flash.html.HTMLLoader;
	import controls.util.ElementUtils;
	
	public class ElementBoundaries
	{
		/**
		* The HTMLLoader the element is in
		*/
		private var htmlLoader:HTMLLoader;
		
		/**
		 * The left offset of the element in the HTML page
		 */
		private var leftOffset:int;
		
		/**
		 * The position of the right edge of the element in the HTML page
		 */
		private var rightOffset:int;
		
		/**
		 * The top offset of the element in the HTML page
		 */
		private var topOffset:int;
		
		/**
		 * The vertical position of the bottom edge of the element in the HTML page
		 */
		private var bottomOffset:int;
		
		/**
		* Finds the bounding rectangle of a character range within a TextField object.  If the character range spans multiple lines, bounding rectangles are calculated for each line.
		* 
		* @param textField The TextField the string is in.
		* @param startIndex The start index of the character range.
		* @param endIndex The end index of the character range.
		* @param xOffset The horizontal offset to apply to the boundary rectangle.
		* @param yOffset The vertical offset to apply to the bounding rectangle.
		*/
		public function ElementBoundaries(htmlLdr:HTMLLoader,left:int, right:int, top:int, bottom:int)
		{
			this.htmlLoader = htmlLdr;
			this.leftOffset = left;
			this.rightOffset = right;
			this.topOffset = top;
			this.bottomOffset = bottom;
		}
		
		/**
		* Returns the bounding rectangles of the current character range.
		*/
		public function get rectangles():Array{
			var rects:Array = [];

			// Only return rects in the visible area of the HTMLLoader
			var firstVisibleIndexV:int = this.htmlLoader.scrollV;
			var lastVisibleIndexV:int = this.htmlLoader.scrollV + this.htmlLoader.height;
			var firstVisibleIndexH:int = this.htmlLoader.scrollH;
			var lastVisibleIndexH:int = this.htmlLoader.scrollH + this.htmlLoader.width;
			
			// If the visible area of the TextField is larger than the actual character range, only return rects within the character range instead.
			firstVisibleIndexV = Math.max(firstVisibleIndexV, this.topOffset);
			lastVisibleIndexV = Math.min(lastVisibleIndexV, this.bottomOffset);
			firstVisibleIndexH = Math.max(firstVisibleIndexH, this.leftOffset);
			lastVisibleIndexH = Math.min(lastVisibleIndexH, this.rightOffset);
			
			var r:Rectangle = new Rectangle(firstVisibleIndexH, firstVisibleIndexV, lastVisibleIndexH - firstVisibleIndexH, lastVisibleIndexV - firstVisibleIndexV);
			rects.push(r);
			return rects;
		}
		
		/**
		* Indicates whether or not the element is visible
		*/
		public function get isVisible():Boolean {
			var scrollH:int = this.htmlLoader.scrollH;
			var scrollV:int = this.htmlLoader.scrollV;
			if((leftOffset > scrollH && leftOffset < scrollH + this.htmlLoader.width) || (rightOffset > scrollH && rightOffset < scrollH + this.htmlLoader.width) ||
			   (topOffset > scrollV && topOffset < scrollV + this.htmlLoader.height) || (bottomOffset > scrollV && bottomOffset < scrollV + this.htmlLoader.height)) {
			   	return true;
			}
			else {
				return false;
			}
		}
	}
}