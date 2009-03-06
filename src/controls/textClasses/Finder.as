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

Modified to support regular expression finding by Andrew Spencer, February 2009
*/
package controls.textClasses
{
	import flash.events.MouseEvent;
	import flash.html.HTMLLoader;
	import controls.util.ElementUtils;

	public class Finder
	{
		/**
		* The HTMLLoader to be searched.
		*/
		private var htmlLoader:HTMLLoader;
		
		/**
		* The current starting index for searches. 
		*/
		private var caratIndex:int;
		
		/**
		* Finds strings in a TextField.
		* 
		* @param textField The TextField to search.
		*/
		public function Finder(htmlLdr:HTMLLoader){
			this.caratIndex = 0;
			this.htmlLoader = htmlLdr;
		}
		
       	/**
		* Returns an array of elements in the HTMLLoader whose source text matches the regular expression.
		* 
		* @param regex The regex to search for.
		* @return An array of all the element matches
		*/
		public function matchesOf(regex:RegExp):Array {
			var matchingElements:Array = [];
			var elements:Object = htmlLoader.window.document.getElementsByTagName("*");
			
			// Only search elements that appear in view -- do not search those above view
			var elementIndex:int = 0;
			while(elementIndex < elements.length && !inView(elements[elementIndex])) {
				elementIndex++;
			}
			
			while(elementIndex < elements.length && inView(elements[elementIndex])) {
				// Look for match in this element
				var matchObject:Object = regex.exec(elements[elementIndex].outerHTML);
				
				// If match is unique to this element, add this element to the matches list
				if(matchObject != null) {
					// trace("Saw outer text match\n");
					if(regex.exec(elements[elementIndex].innerHTML) == null) {
        	   			matchingElements.push(elements[elementIndex]);
     				}
        	   	}
        	   	
        	   	elementIndex++;
            }
            
            trace("Number of matches = "+matchingElements.length);
            
            return matchingElements;
		}
		
		
        /**
        * Returns true if the parameter HTML element is visible in the member HTMLLoader
        * 
        * @param string The HTML element to test
        * @return True if the parameter element is visible and False if not
        */
        public function inView(element:Object):Boolean{
        	if(ElementUtils.elementOverlapsVerticalBounds(element, this.htmlLoader.scrollV, this.htmlLoader.scrollV+this.htmlLoader.height) ||
        	   ElementUtils.elementOverlapsHorizontalBounds(element, this.htmlLoader.scrollH, this.htmlLoader.scrollH+this.htmlLoader.height)) {
        	   	return true;
        	   }
        	else {
        		return false;
        	}
        }
    }
}