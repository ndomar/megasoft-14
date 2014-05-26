package com.megasoft.entangle;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;

import com.megasoft.config.Config;

public class PagerAdapter extends FragmentStatePagerAdapter {

	int tangleId;
	int userId;
	Context context;
	private String tangleName;
	private boolean isTangleOwner;

	final static String STREAM = "Stream";

	public PagerAdapter(Context con, FragmentManager fm, int tangleId,
			int userId, String tangleName, boolean isTangleOwner) {
		super(fm);
		this.context = con;
		this.tangleName = tangleName;
		this.tangleId = tangleId;
		this.userId = userId;
		this.isTangleOwner = isTangleOwner;
	}

	/**
	 * Initialize the navigation drawer (sidebar menu)
	 * 
	 * @param
	 * @return
	 * @author Mohamed Farghal
	 */
	@Override
	public Fragment getItem(int index) {

		Bundle args = new Bundle();
		args.putInt("tangleId", tangleId);
		args.putString("tangleName", tangleName);
		Fragment fragment = null;
		switch (index) {
		case 0:
			fragment = new TangleFragment();
			break;
		case 1:
			fragment = new MemberListFragment();
			args.putInt(Config.TANGLE_ID, tangleId);
			break;
		case 2:
			fragment = new ProfileSuperFragment();
			args.putInt("userId", userId);
			break;
		case 3:
			fragment = new MyRequestsFragment();
			break;
		case 4:		
			fragment = new MyOffersFragment();
			break;
		case 5:
			if(isTangleOwner){
				fragment = new ManagePendingInvitationFragment();
			}
			break;
		default:
			break;
		}
		fragment.setArguments(args);
		return fragment;
	}

	@Override
	public int getCount() {
		if (isTangleOwner) {
			return 6;
		} else {
			return 5;
		}

	}

	@Override
	public CharSequence getPageTitle(int position) {
		switch (position) {
		case 0:
			return "Stream";

		case 1:
			return "Members";
		case 2:
			return "You";

		case 3:
			return "My Requests";
		case 4:
			return "My Offers";
		case 5:
			return "Tangle Management";

		default:
			return "Tab " + position;
		}
	}
}
