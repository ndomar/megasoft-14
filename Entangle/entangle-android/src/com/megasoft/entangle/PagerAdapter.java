package com.megasoft.entangle;

import com.megasoft.entangle.megafragments.TangleFragment;
import com.megasoft.requests.GetRequest;

import android.content.Context;
import android.graphics.drawable.Drawable;
import android.graphics.drawable.ScaleDrawable;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.text.Spannable;
import android.text.SpannableStringBuilder;
import android.text.style.ImageSpan;
import android.util.Log;

public class PagerAdapter extends FragmentStatePagerAdapter {

	Context context;
	
	public PagerAdapter(Context con, FragmentManager fm) {
		super(fm);
		this.context = con;
	}

	@Override
	public Fragment getItem(int index) {
		TangleFragment fragment = new TangleFragment();
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
