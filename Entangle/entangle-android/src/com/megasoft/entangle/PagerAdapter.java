package com.megasoft.entangle;

import com.megasoft.config.Config;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.util.Log;

public class PagerAdapter extends FragmentStatePagerAdapter {

	Context context;
	private String tangleId;
	private int userId;
	
	public PagerAdapter(Context con, FragmentManager fm, String tangleId,int userId) {
		super(fm);
		this.tangleId = tangleId;
		this.userId = userId;
		this.context = con;
	}

	@Override
	public Fragment getItem(int index) {
		Fragment fragment = null;
		Bundle args = new Bundle();
		if (index == 0) {
			fragment = new TangleFragment();
        } else if (index == 1) {
        	fragment = new ProfileFragment();
        	args.putInt("userId", userId);
        }
		args.putString("key", tangleId);
		
		fragment.setArguments(args);
		return fragment;
	}
        
	@Override
	public int getCount() {
		// TODO Auto-generated method stub
		return 2;
	}
	
	@Override
	public CharSequence getPageTitle(int position) {
        String title = "";
        if (position == 0) {
        	title = "Requests";
        } else if (position == 1) {
        	title = "You";
        }
        return title;
    }

}
