package com.megasoft.entangle;

import android.app.Activity;
import android.app.Fragment;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.ViewPager;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class SampleFragment extends Fragment {

	private PagerAdapter tab;
	private FragmentActivity activity;
	private ViewPager pager;
	
	
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
		
        View view = inflater.inflate(R.layout.fragment_sample, container, false);
//        ((TextView) view.findViewById(R.id.sample)).setText(getArguments().getString("key"));
        
        tab = new PagerAdapter(activity.getSupportFragmentManager());
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
