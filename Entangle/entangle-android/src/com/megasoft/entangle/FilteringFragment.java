package com.megasoft.entangle;

import java.util.HashMap;

import android.annotation.SuppressLint;
import android.app.DialogFragment;
import android.os.Bundle;
import android.view.ContextMenu;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ContextMenu.ContextMenuInfo;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;

@SuppressLint("NewApi")
public class FilteringFragment extends DialogFragment {

	private String rootResource = "http://entangle2.apiary.io/";

	private HashMap<String, Integer> userToId = new HashMap<String, Integer>();

	private HashMap<String, Integer> tagToId = new HashMap<String, Integer>();

	private AutoCompleteTextView tagText;
	private AutoCompleteTextView userText;
	private EditText fullText;

	public static FilteringFragment createInstance(
			HashMap<String, Integer> tagToId, HashMap<String, Integer> userToId) {
		FilteringFragment fragment = new FilteringFragment();
		fragment.tagToId = tagToId;
		fragment.userToId = userToId;
		return fragment;
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		View view = inflater.inflate(R.layout.filtering_fragment, container,
				false);
		setTagSuggestions(view);
		setUserSuggestions(view);
		setFullText(view);
		setButtonsActions(view);
		getDialog().setTitle("Filtering Options");
		return view;
	}

	private void setFullText(View view) {
		fullText = (EditText) view.findViewById(R.id.fullTextValue);
	}

	private void setTagSuggestions(View view) {
		tagText = (AutoCompleteTextView) view.findViewById(R.id.tagValue);
		tagText.setAdapter(new ArrayAdapter<String>(getActivity(),
				android.R.layout.simple_list_item_1,
				((TangleProfilePage) getActivity()).getTagsSuggestions()));
	}

	private void setUserSuggestions(View view) {
		userText = (AutoCompleteTextView) view.findViewById(R.id.userValue);
		userText.setAdapter(new ArrayAdapter<String>(getActivity(),
				android.R.layout.simple_list_item_1,
				((TangleProfilePage) getActivity()).getUsersSuggestions()));
	}

	private void setButtonsActions(View view) {
		Button filter = (Button) view.findViewById(R.id.doFilteration);
		filter.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				String url = "";
				String tag = tagText.getText().toString();
				String user = userText.getText().toString();
				String text = fullText.getText().toString();
				boolean putAnd = false;
				boolean putQuestionMark = false;
				if (userToId != null && userToId.containsKey(user)) {
					putQuestionMark = true;
					putAnd = true;
					url += "userid" + userToId.get(user);
				}
				if (tag != "" && tagToId != null && tagToId.containsKey(tag)) {
					putQuestionMark = true;
					if (putAnd) {
						url += "&";
					}
					putAnd = true;
					url += "tagid" + tagToId.get(tag);
				}
				if (text != "") {
					putQuestionMark = true;
					if (putAnd) {
						url += "&";
					}
					url += "&fulltext=" + text;
				}
				if (putQuestionMark) {
					url = "?" + url;
				}
				url = rootResource + "tangle/"
						+ ((TangleProfilePage) getActivity()).getTangleId()
						+ "/request" + url;
				((TangleProfilePage) getActivity()).sendFilteredRequest(url
						.replace(" ", "+"));
				getDialog().dismiss();
			}
		});
		Button cancel = (Button) view.findViewById(R.id.cancelFilteration);
		cancel.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				getDialog().dismiss();
			}
		});
	}
}
