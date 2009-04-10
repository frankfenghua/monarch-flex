package
{
	import mx.collections.ArrayCollection;
	
	public class cAccountInformation
	{
		
		public function cAccountInformation(fullName:String,
											userName:String,
											password:String,
											userId:int,
											communities:ArrayCollection)
		{
			mFullName = fullName;
			mUserName = userName;
			mPassword = password;
			mUserId = userId;	
			
			mSelectedCommunityId = -1;
			mSelectedCommunityName = "";
			
			mCommunities = communities;
		}
		
		/* 
		 *
		 */
		public function getCommunityId(communityName:String):int
		{
			var id:int = -1;
			for(var i:int = 0; i < mCommunities.length; i++) {
					var communityGroupEntry:cCommunityGroupEntry = cCommunityGroupEntry(mCommunities.getItemAt(i));
					if(communityGroupEntry.mName == communityName) {
						id = communityGroupEntry.mId;
					}
			}
			return id;
		}
		
		public var mFullName:String;
		public var mUserName:String;
		public var mPassword:String;
		public var mUserId:int;
		
		public var mSelectedCommunityId:int;
		public var mSelectedCommunityName:String;
		
		public var mCommunities:ArrayCollection;

	}
}