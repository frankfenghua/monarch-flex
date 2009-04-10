package
{
	import flash.events.Event;

	public class cEditWebsiteEvent extends Event
	{
		public function cEditWebsiteEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//						   DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		// the name of the website 
		public var mWebsiteName:String = "";
		// the name of the community group
		public var mCommunityGroupName:String = "";
		
	}
}