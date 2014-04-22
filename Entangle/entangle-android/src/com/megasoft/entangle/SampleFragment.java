package com.megasoft.entangle;

import com.megasoft.config.Config;

import android.app.ActionBar;
import android.app.Activity;
import android.app.Fragment;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.ViewPager;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

public class SampleFragment extends Fragment {

	private PagerAdapter tab;
	private FragmentActivity activity;
	private View view;
	private ViewPager pager;
	private ActionBar actionBar;
	
	
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
		
        view = inflater.inflate(R.layout.fragment_sample, container, false);
//        ((TextView) view.findViewById(R.id.sample)).setText(getArguments().getString("key"));
        
    	SharedPreferences settings = activity.getSharedPreferences(Config.SETTING, 0);
		int userId = settings.getInt(Config.USER_ID, -1);
        tab = new PagerAdapter(activity, activity.getSupportFragmentManager(), getArguments().getString("key"),userId);
        pager = (ViewPager) view.findViewById(R.id.pager);
        pager.setAdapter(tab);
       
        
        return view;
    }
	
	@Override
	public void onAttach(Activity activity) {
	    this.activity = (FragmentActivity) activity;
	    super.onAttach(this.activity);
	}
	
}
