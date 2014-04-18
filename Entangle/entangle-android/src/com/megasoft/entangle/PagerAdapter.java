package com.megasoft.entangle;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.util.Log;

public class PagerAdapter extends FragmentStatePagerAdapter {

	public PagerAdapter(FragmentManager fm) {
		super(fm);
	}

	@Override
	public Fragment getItem(int arg0) {
		Fragment fragment = new SampleTab();
        return fragment;
	}
        
	@Override
	public int getCount() {
		// TODO Auto-generated method stub
		return 2;
	}

}
