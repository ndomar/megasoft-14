package com.megasoft.entangle;

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
	
	public PagerAdapter(Context con, FragmentManager fm,int tangleId,int userId) {
		super(fm);
		this.context = con;
		this.tangleId = tangleId;
		this.userId = userId;
	}

	@Override
	public Fragment getItem(int index) {
		Bundle args = new Bundle();
		args.putInt("tangleId", tangleId);
		Fragment fragment = null;
		switch (index) {
		case 0:
			fragment = new SampleTab();
			break;
		case 1:
			fragment = new ProfileFragment();
			args.putInt("userId", userId);
			Log.e("test2", tangleId+"");
			fragment.setArguments(args);
			break;

		default:
			break;
		}
        return fragment;
	}
        
	@Override
	public int getCount() {
		return 2;
	}
	
	@Override
	public CharSequence getPageTitle(int position) {
		switch (position) {
		case 0:
			return "Tangle Stream";
		
		case 1:
			return "Profile";

		default:
			return "Tab " + position;
		}
    }

}
