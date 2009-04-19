package
{
	import mx.collections.ArrayCollection;
	
	public class cAccountInformation
	{
		/**
		 * 	Constructor.
		 */
		public function cAccountInformation(fullName:String,
											userName:String,
											password:String,
											userId:int,
											communities:ArrayCollection,
											otherCommunities:ArrayCollection)
		{
			mFullName = fullName;
			mUserName = userName;
			mPassword = password;
			mUserId = userId;	
			
			mSelectedCommunityId = -1;
			mSelectedCommunityName = "";
			
			mCommunities = communities;
			mOtherCommunities = otherCommunities;
		}
		
		/**
		 * 	Function whci returns the community id for a particular community.
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
		// user information
		public var mFullName:String = "";
		public var mUserName:String = "";
		public var mPassword:String = "";
		public var mUserId:int = -1;
		
		public var mSelectedCommunityId:int = -1;
		public var mSelectedCommunityName:String = "";
		// collection of cCommunityGroupEntry objects which belong to the user
		public var mCommunities:ArrayCollection = null;
		// collection of cCommunityGroupEntry objects which don't belong to the user
		public var mOtherCommunities:ArrayCollection = null;
	}
}