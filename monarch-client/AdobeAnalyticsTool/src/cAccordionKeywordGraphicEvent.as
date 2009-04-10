package
{
	import flash.events.Event;

	public class cAccordionKeywordGraphicEvent extends Event
	{
		public var KEYWORD_CHECKED:String = "keywordChecked";
		public var KEYWORD_UNCHECKED:String = "keywordUnchecked";
		
		public function cAccordionKeywordGraphicEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//							  DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		// keyword name
		public var mKeywordName:String = "";
		// the type of event
		public var mType:String = "";
		
	}
}