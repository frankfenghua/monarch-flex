package
{
	import flash.events.Event;

	public class cAccordionWebsiteGraphicEvent extends Event
	{
		public var OPEN_ADDITIONAL_INFORMATION:String = "openAdditionalInformation";
		public var WEBSITE_CHECKED:String = "websiteChecked";
		public var WEBSITE_UNCHECKED:String = "websiteUnchecked";
		
		public function cAccordionWebsiteGraphicEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
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
		// the type of event
		public var mType:String = "";
		
	}
}