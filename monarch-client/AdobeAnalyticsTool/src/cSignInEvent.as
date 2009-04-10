/**
 * 
 * 
 */

package
{
	import flash.events.Event;

	public class cSignInEvent extends Event
	{
		public var SIGN_IN:String = "signIn";
		public var CANCEL:String = "cancel";
		
		public function cSignInEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		///////////////////////////////////////////////////////////////////////////////////////////
		//
		//									DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////////////
		
		public var mType:String = "";
		public var mEmail:String = "";
		public var mPassword:String = "";
	}
}