package com.megasoft.entangle;

import android.content.Context;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;

public class PagerAdapter extends FragmentStatePagerAdapter {

	Context context;
	
	public PagerAdapter(Context con, FragmentManager fm) {
		super(fm);
		this.context = con;
	}

	@Override
	public Fragment getItem(int index) {
		Fragment fragment = new TangleFragment();
        return fragment;
	}
        
	@Override
	public int getCount() {
		// TODO Auto-generated method stub
		return 2;
	}
	
	@Override
	public CharSequence getPageTitle(int position) {
        return "Tab " + position;
    }

}
