package com.megasoft.entangle;

import android.app.Activity;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.ViewPager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.SearchView;

import com.megasoft.config.Config;

public class SampleFragment extends Fragment {

	private PagerAdapter tab;
	private FragmentActivity activity;
	private View view;
	private ViewPager pager;
	private int tangleId;
	private int userId;
	private String tangleName;
	private boolean isTangleOwner;
	
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
		
        view = inflater.inflate(R.layout.fragment_pager, container, false);
        
        SharedPreferences settings = activity.getSharedPreferences(Config.SETTING, 0);
		userId = settings.getInt(Config.USER_ID, -1);
		tangleId = getArguments().getInt("tangleId");
		tangleName = getArguments().getString("tangleName");
		isTangleOwner = getArguments().getBoolean("isTangleOwner");
        tab = new PagerAdapter(activity, activity.getSupportFragmentManager(),tangleId,userId, tangleName,isTangleOwner);
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
