package
{
	import flash.events.Event;

	public class cAdditionalWebsiteInformationEvent extends Event
	{
		public function cAdditionalWebsiteInformationEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//						   DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		// website name
		public var mWebsiteName:String = "";
		// community group name
		public var mCommuniutyGroupName:String = "";
		
	}
}