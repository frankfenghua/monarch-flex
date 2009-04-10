/**
 * 
 * 
 * 
 * 
 */

package
{
	import flash.events.Event;

	public class cCreateCommunityGroupEvent extends Event
	{
		public function cCreateCommunityGroupEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//								DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		// name of the community 
		public var mCommName:String = "";
		// the array of keywords to register under this community
		public var mKeywords:Array = new Array();
		
	}
}