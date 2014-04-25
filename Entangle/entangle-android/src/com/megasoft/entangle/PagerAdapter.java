package com.megasoft.entangle;

import com.megasoft.config.Config;
import com.megasoft.entangle.megafragments.TangleFragment;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.util.Log;

public class PagerAdapter extends FragmentStatePagerAdapter {
	
	int tangleId;
	int userId;
	Context context;
	private String tangleName;
	final static String STREAM = "Stream";
	public PagerAdapter(Context con, FragmentManager fm,int tangleId,int userId, String tangleName) {
		super(fm);
		this.context = con;
		this.tangleName = tangleName;
		this.tangleId = tangleId;
		this.userId = userId;
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
			fragment = new ProfileFragment();
			args.putInt("userId", userId);
			break;

		default:
			break;
		}
		fragment.setArguments(args);
        return fragment;
	}
        
	@Override
	public int getCount() {
		return 3;
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

		default:
			return "Tab " + position;
		}
    }

}
